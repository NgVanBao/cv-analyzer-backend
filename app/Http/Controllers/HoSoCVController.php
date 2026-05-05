<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HoSoCV;

class HoSoCVController extends Controller
{
    public function index()
    {
        return response()->json(HoSoCV::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'MaTaiKhoan' => 'required|integer|exists:NguoiDung,MaTaiKhoan',
            'file_cv' => 'required|mimes:pdf|max:5120', // Bắt buộc file PDF, tối đa 5MB
        ]);

        if ($request->hasFile('file_cv')) {
            $file = $request->file('file_cv');
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Lưu file vào thư mục storage/app/public/cvs
            $filePath = $file->storeAs('cvs', $fileName, 'public');

            // Tạo bản ghi trong DB
            $hoSoCV = HoSoCV::create([
                'MaTaiKhoan' => $request->MaTaiKhoan,
                'TenFile' => $fileName,
                'DuongDanFile' => $filePath,
                'TrangThaiXuLy' => 'Đang xử lý',
            ]);

            // Đẩy Job phân tích AI vào hàng đợi (chạy ngầm)
            \App\Jobs\ProcessCVExtraction::dispatch($hoSoCV->MaCV);

            return response()->json([
                'message' => 'Tải lên CV thành công! Hệ thống đang dùng AI để phân tích ngầm...',
                'data' => $hoSoCV
            ], 201);
        }

        return response()->json([
            'message' => 'Không tìm thấy file tải lên.'
        ], 400);
    }

    public function show($id)
    {
        return response()->json(HoSoCV::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $hoSoCV = HoSoCV::findOrFail($id);
        $validated = $request->validate([
            'MaTaiKhoan' => 'required|integer|exists:NguoiDung,MaTaiKhoan',
            'TenFile' => 'required|string|max:255',
            'DuongDanFile' => 'required|string|max:255',
            'DuLieuAITrichXuat' => 'nullable|string',
            'TrangThaiXuLy' => 'required|string|max:50',
            'TrinhDoHocVan' => 'nullable|string|max:100',
            'KinhNghiem' => 'nullable|string',
        ]);
        $hoSoCV->update($validated);
        return response()->json([
            'message' => 'Cập nhật hồ sơ CV thành công!',
            'data' => $hoSoCV
        ]);
    }

    public function destroy($id)
    {
        $hoSoCV = HoSoCV::findOrFail($id);
        $hoSoCV->delete();
        return response()->json([
            'message' => 'Xóa hồ sơ CV thành công!'
        ]);
    }
}
