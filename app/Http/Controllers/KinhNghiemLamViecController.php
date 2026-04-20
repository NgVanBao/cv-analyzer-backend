<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KinhNghiemLamViec;

class KinhNghiemLamViecController extends Controller
{
    public function index()
    {
        return response()->json(KinhNghiemLamViec::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'MaCV' => 'required|integer|exists:ho_so_cv,MaCV',
            'TenCongTy' => 'required|string|max:255',
            'ViTriCongTac' => 'required|string|max:255',
            'ThoiGianTu' => 'required|date',
            'ThoiGianDen' => 'nullable|date|after_or_equal:ThoiGianTu',
            'MoTaChiTiet' => 'nullable|string',
        ]);
        $kinhNghiem = KinhNghiemLamViec::create($validated);
        return response()->json([
            'message' => 'Thêm kinh nghiệm thành công!',
            'data' => $kinhNghiem
        ], 201);
    }

    public function show($id)
    {
        return response()->json(KinhNghiemLamViec::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $kinhNghiem = KinhNghiemLamViec::findOrFail($id);
        $validated = $request->validate([
            'MaCV' => 'required|integer|exists:ho_so_cv,MaCV',
            'TenCongTy' => 'required|string|max:255',
            'ViTriCongTac' => 'required|string|max:255',
            'ThoiGianTu' => 'required|date',
            'ThoiGianDen' => 'nullable|date|after_or_equal:ThoiGianTu',
            'MoTaChiTiet' => 'nullable|string',
        ]);
        $kinhNghiem->update($validated);
        return response()->json([
            'message' => 'Cập nhật thành công!',
            'data' => $kinhNghiem
        ]);
    }

    public function destroy($id)
    {
        $kinhNghiem = KinhNghiemLamViec::findOrFail($id);
        $kinhNghiem->delete();
        return response()->json([
            'message' => 'Xóa thành công!'
        ]);
    }
}
