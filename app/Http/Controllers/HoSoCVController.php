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
        $validated = $request->validate([
            'MaTaiKhoan' => 'required|integer|exists:nguoi_dung,MaTaiKhoan',
            'TenFile' => 'required|string|max:255',
            'DuongDanFile' => 'required|string|max:255',
            'DuLieuAITrichXuat' => 'nullable|string',
            'TrangThaiXuLy' => 'required|string|max:50',
            'TrinhDoHocVan' => 'nullable|string|max:100',
            'KinhNghiem' => 'nullable|string',
            'KyNang' => 'nullable|string',
        ]);
        $hoSoCV = HoSoCV::create($validated);
        return response()->json([
            'message' => 'Tạo hồ sơ CV thành công!',
            'data' => $hoSoCV
        ], 201);
    }

    public function show($id)
    {
        return response()->json(HoSoCV::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $hoSoCV = HoSoCV::findOrFail($id);
        $validated = $request->validate([
            'MaTaiKhoan' => 'required|integer|exists:nguoi_dung,MaTaiKhoan',
            'TenFile' => 'required|string|max:255',
            'DuongDanFile' => 'required|string|max:255',
            'DuLieuAITrichXuat' => 'nullable|string',
            'TrangThaiXuLy' => 'required|string|max:50',
            'TrinhDoHocVan' => 'nullable|string|max:100',
            'KinhNghiem' => 'nullable|string',
            'KyNang' => 'nullable|string',
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
