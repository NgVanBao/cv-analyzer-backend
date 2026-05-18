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

        $selectedKey = $keys[array_rand($keys)];

        //  Lấy danh sách kỹ năng hiện có trong Từ điển để AI "chuẩn hóa"
        $existingSkills = \App\Models\TuDienKyNang::pluck('TenKyNang')->toArray();
        $skillsListStr = implode(', ', $existingSkills);

        $prompt = "Bạn là một chuyên gia phân tích hồ sơ xin việc (CV). Hãy đọc đoạn văn bản CV sau và trích xuất thông tin thành ĐÚNG định dạng JSON theo cấu trúc sau. 
        
        LƯU Ý QUAN TRỌNG VỀ KỸ NĂNG:
        - Tôi có một danh sách kỹ năng chuẩn trong hệ thống: [{$skillsListStr}].
        - Khi trích xuất kỹ năng, hãy cố gắng khớp chúng với danh sách chuẩn này. Nếu một kỹ năng trong CV tương đương với một kỹ năng trong danh sách (VD: 'Laravel' tương đương 'PHP Laravel'), hãy dùng tên trong danh sách chuẩn.
        - Chỉ khi nào kỹ năng đó hoàn toàn mới và không có trong danh sách thì mới tự tạo tên mới (ngắn gọn, 1-3 từ).
        
        QUY TẮC PHÂN LOẠI MỨC ĐỘ (level):
        - Cơ bản: Mới bắt đầu, kiến thức nền tảng hoặc kinh nghiệm dưới 1 năm.
        - Khá: Sử dụng thành thạo, có 1-3 năm kinh nghiệm hoặc có project cá nhân/thực tế.
        - Giỏi: Hiểu sâu sắc, trên 3 năm kinh nghiệm hoặc vị trí Senior/Leader.
        - Xuất sắc: Chuyên gia, có chứng chỉ cao cấp hoặc trên 5 năm kinh nghiệm dày dặn.

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

        // Tối ưu: Lấy tất cả kỹ năng của CV một lần duy nhất để tránh truy vấn trong vòng lặp (N+1)
        $cvSkillsMap = $cv->kyNangTrongCVs()->get()->keyBy('MaKyNang');

        foreach ($requiredSkills as $req) {
            $weight = $req->TrongSoDiem ?? 1;
            $totalRequiredScore += $weight;

            // Tìm kỹ năng trong CV từ Map đã lấy sẵn
            $cvSkill = $cvSkillsMap->get($req->MaKyNang);

            if ($cvSkill) {
                $multiplier = $levelMultipliers[$cvSkill->MucDo] ?? 0.5;
                $achievedScore += ($weight * $multiplier);
            }
        }

        if ($totalRequiredScore == 0)
            return 0;

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

        // Xóa gợi ý cũ (Trừ các đánh giá Custom JD do người dùng tự nhập)
        $customJobIds = \App\Models\TinTuyenDung::where('TrangThai', 'Custom')->pluck('MaTuyenDung');
        \App\Models\KetQuaGoiY::where('MaCV', $cv->MaCV)
            ->whereNotIn('MaTuyenDung', $customJobIds)
            ->delete();

        // Sắp xếp các job theo điểm cao nhất trước để lấy Top phân tích chi tiết
        $scoredJobs = [];
        foreach ($jobs as $job) {
            $score = $this->calculateMatchScore($cv, $job);
            if ($score > 0) {
                $scoredJobs[] = ['job' => $job, 'score' => $score];
            }
        }

        // Sắp xếp giảm dần theo điểm
        usort($scoredJobs, fn($a, $b) => $b['score'] <=> $a['score']);

        $countDetailed = 0;
        foreach ($scoredJobs as $item) {
            $job = $item['job'];
            $score = $item['score'];

            $phanTich = null;
            // Chỉ phân tích chi tiết cho Top 3 công việc có điểm >= 40% để tránh quá tải API
            if ($score >= 40 && $countDetailed < 3) {
                // Nghỉ 2 giây để tránh lỗi Rate Limit (429 Too Many Requests)
                sleep(2);
                $phanTich = $this->generateAIRecommendation($cv, $job);
                if ($phanTich)
                    $countDetailed++;
            }

            \App\Models\KetQuaGoiY::create([
                'MaCV' => $cv->MaCV,
                'MaTuyenDung' => $job->MaTuyenDung,
                'TyLePhuHop' => $score,
                'PhanTichChiTiet' => $phanTich
            ]);
        }
    }

    /**
     * Gọi AI để phân tích sâu sự phù hợp giữa CV và một Job cụ thể.
     */
    public function generateAIRecommendation(HoSoCV $cv, TinTuyenDung $job): ?array
    {
        $keys = array_filter([env('GEMINI_API_KEY'), env('GEMINI_API_KEY_2'), env('GEMINI_API_KEY_3'), env('GEMINI_API_KEY_4'), env('GEMINI_API_KEY_5')]);
        if (empty($keys))
            return null;

        $selectedKey = $keys[array_rand($keys)];

        $cvData = $cv->DuLieuAITrichXuat;
        $jobDescription = $job->MoTaChiTiet;

        $prompt = "Bạn là chuyên gia tư vấn nghề nghiệp. Hãy so sánh dữ liệu CV và Mô tả công việc (JD) sau đây:
        
        DỮ LIỆU CV (JSON):
        {$cvData}
        
        MÔ TẢ CÔNG VIỆC (JD):
        {$jobDescription}
        
        Hãy phân tích và trả về ĐÚNG định dạng JSON sau (không kèm giải thích):
        {
          \"kynang_phuhop\": [\"kỹ năng 1\", \"kỹ năng 2\"],
          \"kynang_thieu\": [\"kỹ năng 1\", \"kỹ năng 2\"],
          \"khuyen_nghi\": \"Lời khuyên ngắn gọn để cải thiện CV hoặc ứng tuyển tốt hơn.\",
          \"lotrinh\": [\"Bước 1: ...\", \"Bước 2: ...\"]
        }";

        try {
            $client = \Gemini::client($selectedKey);
            $response = $client->generativeModel('gemini-flash-latest')->generateContent($prompt);

            $text = $response->text();

            Log::info('Gemini AI Response Text: ' . $text);

            $cleanJson = preg_replace('/```json|```/', '', $text);
            return json_decode(trim($cleanJson), true);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo khuyến nghị AI: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Gọi AI để phân tích trực tiếp (Real-time) sự phù hợp giữa nội dung text CV và đoạn text JD tùy chọn.
     */
    public function analyzeCustomJD(string $cvText, string $jdText): ?array
    {
        $keys = array_filter([
            env('GEMINI_API_KEY'),
            env('GEMINI_API_KEY_2'),
            env('GEMINI_API_KEY_3'),
            env('GEMINI_API_KEY_4'),
            env('GEMINI_API_KEY_5')
        ]);
        if (empty($keys)) {
            Log::error('Không tìm thấy Gemini API Key.');
            return null;
        }

        $selectedKey = $keys[array_rand($keys)];

        $prompt = "Bạn là một chuyên gia tuyển dụng và tư vấn nghề nghiệp cấp cao. Hãy đọc kỹ nội dung CV của ứng viên và Mô tả công việc (JD) tùy chọn dưới đây:
        
        --- NỘI DUNG CV ---
        {$cvText}
        
        --- MÔ TẢ CÔNG VIỆC (JD) ---
        {$jdText}
        
        Hãy phân tích chi tiết mức độ đáp ứng của ứng viên so với yêu cầu JD, sau đó trả về kết quả dưới dạng JSON (KHÔNG bọc trong markdown, KHÔNG thêm bất kỳ giải thích nào ngoài chuỗi JSON) theo đúng cấu trúc sau:
        {
          \"tyle_phuhop\": 85, // Số nguyên từ 0 đến 100 thể hiện phần trăm phù hợp tổng quan
          \"kynang_phuhop\": [\"kỹ năng 1\", \"kỹ năng 2\"], // Các kỹ năng/yêu cầu trong JD mà CV đáp ứng tốt
          \"kynang_thieu\": [\"kỹ năng 1\", \"kỹ năng 2\"], // Các kỹ năng/yêu cầu quan trọng trong JD mà CV chưa có hoặc còn yếu
          \"khuyen_nghi\": \"Đoạn nhận xét ngắn gọn (khoảng 2-3 câu) đánh giá ưu nhược điểm và gợi ý chỉnh sửa CV để trúng tuyển.\",
          \"lotrinh\": [
            \"Bước 1: ...\",
            \"Bước 2: ...\",
            \"Bước 3: ...\"
          ] // Đề xuất các bước hành động cụ thể để ứng viên bổ sung kiến thức hoặc cải thiện kỹ năng còn thiếu
        }";

        try {
            $client = \Gemini::client($selectedKey);
            $response = $client->generativeModel('gemini-flash-latest')->generateContent($prompt);

            $text = $response->text();
            Log::info('Gemini AI Custom JD Analysis text: ' . $text);

            $cleanJson = preg_replace('/```json|```/', '', $text);
            $cleanJson = trim($cleanJson);

            $data = json_decode($cleanJson, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            } else {
                Log::error('Lỗi parse JSON trong analyzeCustomJD: ' . json_last_error_msg());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi gọi AI analyzeCustomJD: ' . $e->getMessage());
            return null;
        }
    }
}
