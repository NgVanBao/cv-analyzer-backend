<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\HoSoCV;
use App\Models\AILog;
use App\Models\HocVan;
use App\Models\KinhNghiemLamViec;
use App\Models\TuDienKyNang;
use App\Models\KyNangTrongCV;
use App\Services\AIService;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;

class ProcessCVExtraction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cvId;

    /**
     * Create a new job instance.
     */
    public function __construct($cvId)
    {
        $this->cvId = $cvId;
    }

    /**
     * Execute the job.
     */
    public function handle(AIService $aiService): void
    {
        $cv = HoSoCV::find($this->cvId);
        
        if (!$cv) {
            return;
        }

        try {
            // Lấy đường dẫn file vật lý (giả sử lưu ở storage/app/public)
            $filePath = storage_path('app/public/' . $cv->DuongDanFile);

            if (!file_exists($filePath)) {
                throw new \Exception("File CV không tồn tại: " . $filePath);
            }

            // 1. Trích xuất Text từ file PDF
            $parser = new Parser();
            $pdf    = $parser->parseFile($filePath);
            $rawText = $pdf->getText();

            // Rút gọn text nếu quá dài (Sử dụng mb_substr để không làm hỏng ký tự có dấu)
            $rawText = mb_substr($rawText, 0, 15000, 'UTF-8');
            
            // Đảm bảo không dính ký tự lỗi không xác định
            $rawText = mb_convert_encoding($rawText, 'UTF-8', 'UTF-8');

            // 2. Gọi AI Service
            $extractedData = $aiService->extractCVData($rawText);

            if (!$extractedData) {
                throw new \Exception("Gemini API không thể bóc tách dữ liệu hợp lệ.");
            }

            // 3. Cập nhật dữ liệu thô vào bảng HoSoCV
            $cv->DuLieuAITrichXuat = json_encode($extractedData, JSON_UNESCAPED_UNICODE);
            $cv->TrinhDoHocVan = mb_substr($extractedData['summary'] ?? null, 0, 100, 'UTF-8');
            // Xóa bớt log cũ nếu có (tránh trùng nếu retry)
            $cv->hocVans()->delete();
            $cv->kinhNghiemLamViecs()->delete();
            $cv->kyNangTrongCVs()->delete();

            // 4. Lưu Học vấn
            if (!empty($extractedData['education']) && is_array($extractedData['education'])) {
                foreach ($extractedData['education'] as $edu) {
                    HocVan::create([
                        'MaCV' => $cv->MaCV,
                        'TenTruong' => $edu['school'] ?? 'Chưa rõ',
                        'ChuyenNganh' => $edu['major'] ?? 'Chưa rõ',
                        'BangCap' => $edu['degree'] ?? 'Chưa rõ',
                        'ThoiGianTu' => $this->parseDate($edu['start_date'] ?? null, true),
                        'ThoiGianDen' => $this->parseDate($edu['end_date'] ?? null),
                    ]);
                }
            }

            // 5. Lưu Kinh nghiệm làm việc
            if (!empty($extractedData['experience']) && is_array($extractedData['experience'])) {
                foreach ($extractedData['experience'] as $exp) {
                    KinhNghiemLamViec::create([
                        'MaCV' => $cv->MaCV,
                        'TenCongTy' => $exp['company'] ?? 'Chưa rõ',
                        'ViTriCongTac' => $exp['position'] ?? 'Chưa rõ',
                        'ThoiGianTu' => $this->parseDate($exp['start_date'] ?? null, true),
                        'ThoiGianDen' => $this->parseDate($exp['end_date'] ?? null),
                        'MoTaChiTiet' => $exp['details'] ?? null,
                    ]);
                }
            }

            // 6. Xử lý Kỹ năng và đồng bộ Từ Điển
            if (!empty($extractedData['skills']) && is_array($extractedData['skills'])) {
                foreach ($extractedData['skills'] as $skill) {
                    $skillName = trim($skill['name'] ?? '');
                    if (empty($skillName)) continue;

                    // Tìm hoặc tạo mới trong Từ Điển
                    $tuDien = TuDienKyNang::firstOrCreate(
                        ['TenKyNang' => $skillName],
                        ['LoaiKyNang' => 'Khác'] // Mặc định nếu AI phát hiện kỹ năng mới
                    );

                    // Thêm vào KyNangTrongCV
                    KyNangTrongCV::create([
                        'MaCV' => $cv->MaCV,
                        'MaKyNang' => $tuDien->MaKyNang,
                        'MucDo' => $skill['level'] ?? 'Cơ bản',
                    ]);
                }
            }

            // 7. Chạy thuật toán gợi ý việc làm (Matching)
            $aiService->suggestJobsForCV($cv);

            // Đánh dấu hoàn thành
            $cv->TrangThaiXuLy = 'Hoàn thành';
            $cv->save();

            // Log thành công
            AILog::create([
                'MaCV' => $cv->MaCV,
                'ThoiGian' => now(),
                'TrangThai' => 'Thành công',
                'NoiDungLog' => 'Trích xuất dữ liệu và gợi ý việc làm thành công.',
            ]);

        } catch (\Exception $e) {
            Log::error("CV Extraction Error [CV ID: {$this->cvId}]: " . $e->getMessage());
            
            $cv->TrangThaiXuLy = 'Lỗi';
            $cv->save();

            AILog::create([
                'MaCV' => $cv->MaCV,
                'ThoiGian' => now(),
                'TrangThai' => 'Lỗi',
                'NoiDungLog' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Helper chuẩn hóa ngày tháng từ AI trả về để tránh lỗi SQL
     */
    private function parseDate($dateStr, $isStart = false)
    {
        if (empty($dateStr) || $dateStr === 'null') {
            return $isStart ? now()->format('Y-m-d') : null; // Nếu startDate rỗng thì lấy hiện tại, endDate rỗng thì null (đang làm)
        }
        
        try {
            return \Carbon\Carbon::parse($dateStr)->format('Y-m-d');
        } catch (\Exception $e) {
            return $isStart ? now()->format('Y-m-d') : null;
        }
    }
}
