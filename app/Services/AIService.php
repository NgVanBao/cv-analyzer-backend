<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;
use App\Models\HoSoCV;
use App\Models\TinTuyenDung;
use App\Models\KetQuaGoiY;
use App\Models\AILog;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Gửi text CV lên Gemini để trích xuất dữ liệu thành JSON.
     */
    public function extractCVData(string $rawText): ?array
    {
        // 1. Lấy danh sách các API Key để xoay vòng (Rotate)
        $keys = array_filter([
            env('GEMINI_API_KEY'),
            env('GEMINI_API_KEY_2'),
            env('GEMINI_API_KEY_3'),
            env('GEMINI_API_KEY_4'),
        ]);

        if (empty($keys)) {
            Log::error('Không tìm thấy Gemini API Key trong cấu hình.');
            return null;
        }

        // 2. Chọn ngẫu nhiên 1 Key để sử dụng
        $selectedKey = $keys[array_rand($keys)];

        // 3. Lấy danh sách kỹ năng hiện có trong Từ điển để AI "chuẩn hóa"
        $existingSkills = \App\Models\TuDienKyNang::pluck('TenKyNang')->toArray();
        $skillsListStr = implode(', ', $existingSkills);

        $prompt = "Bạn là một chuyên gia phân tích hồ sơ xin việc (CV). Hãy đọc đoạn văn bản CV sau và trích xuất thông tin thành ĐÚNG định dạng JSON theo cấu trúc sau. 
        
        LƯU Ý QUAN TRỌNG VỀ KỸ NĂNG:
        - Tôi có một danh sách kỹ năng chuẩn trong hệ thống: [{$skillsListStr}].
        - Khi trích xuất kỹ năng, hãy cố gắng khớp chúng với danh sách chuẩn này. Nếu một kỹ năng trong CV tương đương với một kỹ năng trong danh sách (VD: 'Laravel' tương đương 'PHP Laravel'), hãy dùng tên trong danh sách chuẩn.
        - Chỉ khi nào kỹ năng đó hoàn toàn mới và không có trong danh sách thì mới tự tạo tên mới (ngắn gọn, 1-3 từ).

        Cấu trúc JSON yêu cầu:
        {
          \"summary\": \"Tóm tắt ngắn gọn về kinh nghiệm và mục tiêu (dưới 50 từ).\",
          \"education\": [
            {
              \"school\": \"Tên trường\",
              \"major\": \"Chuyên ngành\",
              \"degree\": \"Bằng cấp (VD: Cử nhân, Thạc sĩ, Kỹ sư)\",
              \"start_date\": \"YYYY-MM-DD\",
              \"end_date\": \"YYYY-MM-DD (hoặc null nếu đang học)\"
            }
          ],
          \"experience\": [
            {
              \"company\": \"Tên công ty\",
              \"position\": \"Vị trí\",
              \"details\": \"Mô tả ngắn gọn công việc\",
              \"start_date\": \"YYYY-MM-DD\",
              \"end_date\": \"YYYY-MM-DD (hoặc null nếu đang làm)\"
            }
          ],
          \"skills\": [
            {
              \"name\": \"Tên kỹ năng (Ưu tiên dùng danh sách chuẩn ở trên)\",
              \"level\": \"Mức độ (Chọn 1 trong: Cơ bản, Khá, Giỏi, Xuất sắc)\"
            }
          ]
        }
        
        Văn bản CV:
        \"\"\"
        {$rawText}
        \"\"\"
        ";

        try {
            // Sử dụng model gemini-flash-latest với Key đã chọn
            $client = \Gemini::client($selectedKey);
            $response = $client->generativeModel('gemini-flash-latest')->generateContent($prompt);
            
            $text = $response->text();
            
            // Xóa markdown json tag nếu Gemini trả về
            $text = str_replace('```json', '', $text);
            $text = str_replace('```', '', $text);
            $text = trim($text);

            $data = json_decode($text, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            } else {
                Log::error('Lỗi parse JSON từ Gemini: ' . json_last_error_msg());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Lỗi kết nối Gemini API: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tính toán tỷ lệ phù hợp giữa CV và một Tin Tuyển Dụng dựa trên kỹ năng.
     * Thuật toán: (Tổng trọng số điểm của kỹ năng CV khớp với Job / Tổng trọng số điểm yêu cầu của Job) * 100
     */
    public function calculateMatchScore(HoSoCV $cv, TinTuyenDung $job): float
    {
        // Lấy danh sách kỹ năng CV đang có (id các kỹ năng)
        $cvSkills = $cv->kyNangTrongCVs()->pluck('MaKyNang')->toArray();
        
        // Lấy danh sách kỹ năng Yêu cầu của Job
        $requiredSkills = $job->kyNangYeuCaus()->get();
        
        if ($requiredSkills->isEmpty()) {
            // Nếu Job không yêu cầu kỹ năng cụ thể nào, cho mặc định 50% hoặc 100% tuỳ logic của bạn. 
            // Tạm thời trả về 0 nếu không có tiêu chí đánh giá.
            return 0;
        }

        $totalRequiredScore = 0;
        $achievedScore = 0;

        // Map mức độ kỹ năng sang trọng số nhân
        $levelMultipliers = [
            'Cơ bản' => 0.5,
            'Khá' => 0.8,
            'Giỏi' => 1.0,
            'Xuất sắc' => 1.2,
        ];

        foreach ($requiredSkills as $req) {
            $weight = $req->TrongSoDiem ?? 1;
            $totalRequiredScore += $weight;

            // Lấy thông tin kỹ năng tương ứng trong CV (nếu có)
            $cvSkill = $cv->kyNangTrongCVs()->where('MaKyNang', $req->MaKyNang)->first();
            
            if ($cvSkill) {
                $multiplier = $levelMultipliers[$cvSkill->MucDo] ?? 0.5;
                $achievedScore += ($weight * $multiplier);
            }
        }

        if ($totalRequiredScore == 0) return 0;

        $matchPercentage = ($achievedScore / $totalRequiredScore) * 100;

        return round(min($matchPercentage, 100), 2);
    }

    /**
     * Chạy so khớp CV với tất cả các tin tuyển dụng đang hoạt động và lưu vào KetQuaGoiY
     */
    public function suggestJobsForCV(HoSoCV $cv): void
    {
        // 1. Lấy tất cả tin tuyển dụng đang mở (TrangThai = 'DangMo')
        $jobs = \App\Models\TinTuyenDung::where('TrangThai', 'DangMo')->get();

        // Xóa gợi ý cũ
        \App\Models\KetQuaGoiY::where('MaCV', $cv->MaCV)->delete();

        foreach ($jobs as $job) {
            $score = $this->calculateMatchScore($cv, $job);

            // Chỉ lưu nếu tỷ lệ phù hợp > 0
            if ($score > 0) {
                \App\Models\KetQuaGoiY::create([
                    'MaCV' => $cv->MaCV,
                    'MaTuyenDung' => $job->MaTuyenDung,
                    'TyLePhuHop' => $score
                ]);
            }
        }
    }
}
