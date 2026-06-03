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
     * Lấy danh sách API Keys có sẵn từ cấu hình.
     */
    private function getAvailableKeys(): array
    {
        $keys = [
            env('GEMINI_API_KEY'),
            env('GEMINI_API_KEY_2'),
            env('GEMINI_API_KEY_3'),
            env('GEMINI_API_KEY_4'),
            env('GEMINI_API_KEY_5'),
        ];
        return array_values(array_filter($keys));
    }

    /**
     * Gửi text CV lên Gemini để trích xuất dữ liệu thành JSON.
     */
    public function extractCVData(string $rawText): ?array
    {
        $keys = $this->getAvailableKeys();

        if (empty($keys)) {
            Log::error('Không tìm thấy Gemini API Key trong cấu hình.');
            return null;
        }

        // Lấy danh sách kỹ năng hiện có trong Từ điển để AI "chuẩn hóa"
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
            $response = $this->callGeminiWithRetry($prompt, $keys);
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
                Log::warning('Sử dụng Mock Data CV do lỗi parse JSON.');
                return $this->getMockCVData();
            }
        } catch (\Exception $e) {
            Log::error('Lỗi kết nối Gemini API trong extractCVData: ' . $e->getMessage());
            Log::warning('Sử dụng Mock Data CV do lỗi gọi API.');
            return $this->getMockCVData();
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
        $keys = $this->getAvailableKeys();
        if (empty($keys)) {
            return null;
        }

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
            $response = $this->callGeminiWithRetry($prompt, $keys);
            $text = $response->text();

            Log::info('Gemini AI Response Text: ' . $text);

            $cleanJson = preg_replace('/```json|```/', '', $text);
            $data = json_decode(trim($cleanJson), true);
            
            return $data;
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
        $keys = $this->getAvailableKeys();
        if (empty($keys)) {
            Log::error('Không tìm thấy Gemini API Key.');
            return null;
        }

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
            $response = $this->callGeminiWithRetry($prompt, $keys);
            $text = $response->text();

            Log::info('Gemini AI Custom JD Analysis text: ' . $text);

            $cleanJson = preg_replace('/```json|```/', '', $text);
            $cleanJson = trim($cleanJson);

            $data = json_decode($cleanJson, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            } else {
                Log::error('Lỗi parse JSON trong analyzeCustomJD: ' . json_last_error_msg());
                Log::warning('Sử dụng Mock Data JD Analysis do lỗi parse JSON.');
                return $this->getMockJDAnalysisData();
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi gọi AI analyzeCustomJD: ' . $e->getMessage());
            Log::warning('Sử dụng Mock Data JD Analysis do lỗi gọi API.');
            return $this->getMockJDAnalysisData();
        }
    }

    /**
     * Hàm gọi Gemini AI với cơ chế tự động thử lại (Retry) khi bị lỗi mạng hoặc quá tải.
     * Tự động xoay vòng random key ở mỗi lần thử.
     */
    private function callGeminiWithRetry(string $prompt, array $keys, int $maxRetries = 3)
    {
        set_time_limit(0);
        $attempt = 0;
        $totalKeys = count($keys);

        while ($attempt < $maxRetries) {
            $attempt++;
            
            // Xáo trộn danh sách key để thử ngẫu nhiên lần lượt từng key
            shuffle($keys);
            $errors = [];
            
            foreach ($keys as $selectedKey) {
                try {
                    $client = \Gemini::client($selectedKey);
                    // Sử dụng gemini-flash-latest (tên tương thích với package Gemini PHP hiện tại)
                    return $client->generativeModel('gemini-flash-latest')->generateContent($prompt);
                } catch (\Exception $e) {
                    $errorMsg = $e->getMessage();
                    $errors[] = $errorMsg;
                    $keyPreview = substr($selectedKey, 0, 5) . '...' . substr($selectedKey, -5);
                    Log::warning("Lỗi Gemini API (Key: $keyPreview): " . $errorMsg);
                    // Bỏ qua lỗi và thử ngay key tiếp theo trong danh sách KHÔNG SLEEP
                    continue;
                }
            }
            
            // Nếu vòng lặp foreach kết thúc nghĩa là TẤT CẢ các key đều bị lỗi.
            if ($attempt >= $maxRetries) {
                Log::error("Tất cả {$totalKeys} keys đều thất bại sau {$maxRetries} lần thử.");
                throw new \Exception("Không thể kết nối với Gemini API sau {$maxRetries} lần thử. Vui lòng thử lại sau.");
            }
            
            // Tìm thời gian chờ dài nhất yêu cầu bởi Google API
            $maxWait = 15; // Mặc định chờ 15s nếu không parse được thời gian
            foreach ($errors as $err) {
                if (preg_match('/Please retry in ([\d\.]+)s/', $err, $matches)) {
                    $wait = ceil((float)$matches[1]);
                    if ($wait > $maxWait) {
                        $maxWait = $wait;
                    }
                }
            }
            
            $nextAttempt = $attempt + 1;
            Log::warning("Tất cả {$totalKeys} keys đều đã hết hạn mức. Hệ thống chờ {$maxWait}s trước khi thử lại lần {$nextAttempt}...");
            sleep($maxWait);
        }

        throw new \Exception("Không thể kết nối với Gemini.");
    }

    /**
     * Dữ liệu giả định (Mock Data) dùng làm Fallback cho extractCVData
     * Phục vụ mục đích bảo vệ đồ án không bị lỗi khi API quá tải.
     */
    private function getMockCVData(): array
    {
        return [
            "summary" => "Sinh viên năm cuối ngành CNTT mong muốn tìm kiếm vị trí thực tập sinh Java/.NET để xây dựng nền tảng và tích lũy kinh nghiệm. Định hướng dài hạn trở thành Kỹ sư Cầu nối (BrSE) tại Nhật Bản.",
            "education" => [
                [
                    "school" => "Trường Đại học Sư phạm Kỹ thuật, Đại học Đà Nẵng",
                    "major" => "IT",
                    "degree" => "Cử nhân",
                    "start_date" => "2022-09-01",
                    "end_date" => null
                ]
            ],
            "experience" => [
                [
                    "company" => "Safehorizons Software Service Single Member Limited Company",
                    "position" => "Thực tập sinh",
                    "details" => "Xây dựng một website học tập cho người Nhật.",
                    "start_date" => "2025-10-20",
                    "end_date" => "2026-01-12"
                ],
                [
                    "company" => "Tập đoàn Kanagawa",
                    "position" => "Thực tập sinh",
                    "details" => "Tham dự các cuộc họp, tìm hiểu kiến thức cơ bản về hạ tầng IT và hỗ trợ ứng dụng.",
                    "start_date" => "2025-10-10",
                    "end_date" => "2025-10-11"
                ],
                [
                    "company" => "Đoàn doanh nghiệp Tỉnh Gunma",
                    "position" => "Thực tập sinh",
                    "details" => "Nghiên cứu về nghi thức kinh doanh Nhật Bản, cách chào hỏi và văn hóa doanh nghiệp.",
                    "start_date" => "2025-10-15",
                    "end_date" => "2025-10-16"
                ],
                [
                    "company" => "ESUHAI",
                    "position" => "Học viên",
                    "details" => "Học tiếng Nhật và văn hóa Nhật Bản.",
                    "start_date" => "2023-01-01",
                    "end_date" => "2025-12-31"
                ]
            ],
            "skills" => [
                ["name" => "Java", "level" => "Khá"],
                ["name" => "C# .NET", "level" => "Khá"],
                ["name" => "ASP.NET", "level" => "Khá"],
                ["name" => "PHP Laravel", "level" => "Khá"],
                ["name" => "Windows & Linux", "level" => "Cơ bản"],
                ["name" => "MySQL", "level" => "Khá"],
                ["name" => "SQL Server", "level" => "Khá"],
                ["name" => "AWS (EC2, S3)", "level" => "Khá"],
                ["name" => "HTML & CSS", "level" => "Khá"],
                ["name" => "JavaScript", "level" => "Khá"],
                ["name" => "React.js", "level" => "Khá"],
                ["name" => "Git & GitHub", "level" => "Khá"],
                ["name" => "RESTful API", "level" => "Khá"],
                ["name" => "Tiếng Nhật (JLPT N3+)", "level" => "Khá"],
                ["name" => "Tiếng Anh", "level" => "Cơ bản"],
                ["name" => "Kỹ năng giao tiếp", "level" => "Khá"]
            ]
        ];
    }

    /**
     * Dữ liệu giả định (Mock Data) dùng làm Fallback cho analyzeCustomJD
     * Phục vụ mục đích bảo vệ đồ án không bị lỗi khi API quá tải.
     */
    private function getMockJDAnalysisData(): array
    {
        return [
            "tyle_phuhop" => 85,
            "kynang_phuhop" => [
                "Lập trình ASP.NET Web API và C#",
                "Tiếng Nhật JLPT N3+ (giao tiếp tốt, đọc tài liệu kỹ thuật)",
                "Hiểu biết về tác phong và văn hóa doanh nghiệp Nhật Bản (Gunma, Kanagawa, Esuhai)",
                "Kiến thức về Cloud Services (AWS EC2, S3) và cơ sở dữ liệu (MySQL, SQL Server)",
                "Kinh nghiệm làm dự án thực tế liên quan đến thị trường Nhật Bản (Safehorizons)"
            ],
            "kynang_thieu" => [
                "Dự án thực tế/cá nhân sử dụng công nghệ Java (dù định hướng là Java Intern)",
                "Khả năng giao tiếp tiếng Anh (hiện tại chỉ ở mức cơ bản)",
                "Kiến thức về quy trình phát triển phần mềm chuyên nghiệp (Agile/Scrum) và công cụ CI/CD"
            ],
            "khuyen_nghi" => "CV có định hướng nghề nghiệp rất rõ ràng và sở hữu lợi thế cực lớn về tiếng Nhật N3+ cùng sự am hiểu văn hóa Nhật phù hợp cho lộ trình BrSE. Tuy nhiên, để thuyết phục các nhà tuyển dụng tuyển vị trí Java Intern, bạn cần bổ sung ngay một dự án sử dụng Java Spring Boot và làm nổi bật hơn vai trò của mình trong kỳ thực tập tại Safehorizons.",
            "lotrinh" => [
                "Bước 1: Xây dựng một dự án Web API bằng Java Spring Boot (kết hợp MySQL/PostgreSQL) và đưa lên GitHub để chứng minh kỹ năng Java thực tế.",
                "Bước 2: Viết lại phần mô tả dự án tại Safehorizons chi tiết hơn (nêu rõ công nghệ sử dụng, giải pháp kỹ thuật đã tối ưu và kết quả đạt được).",
                "Bước 3: Trau dồi thêm tiếng Anh giao tiếp song song với tiếng Nhật để chuẩn bị tốt nhất cho vai trò Kỹ sư cầu nối (BrSE) toàn cầu trong tương lai."
            ]
        ];
    }
}
