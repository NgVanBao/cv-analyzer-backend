<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HocVan;

class HocVanController extends Controller
{
    public function index()
    {
        return response()->json(HocVan::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'MaCV' => 'required|integer|exists:ho_so_cv,MaCV',
            'TenTruong' => 'required|string|max:255',
            'ChuyenNganh' => 'required|string|max:255',
            'BangCap' => 'required|string|max:100',
            'ThoiGianTu' => 'required|date',
            'ThoiGianDen' => 'nullable|date|after_or_equal:ThoiGianTu',
        ]);
        $hocVan = HocVan::create($validated);
        return response()->json([
            'message' => 'Thêm học vấn thành công!',
            'data' => $hocVan
        ], 201);
    }

    public function show($id)
    {
        return response()->json(HocVan::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $hocVan = HocVan::findOrFail($id);
        $validated = $request->validate([
            'MaCV' => 'required|integer|exists:ho_so_cv,MaCV',
            'TenTruong' => 'required|string|max:255',
            'ChuyenNganh' => 'required|string|max:255',
            'BangCap' => 'required|string|max:100',
            'ThoiGianTu' => 'required|date',
            'ThoiGianDen' => 'nullable|date|after_or_equal:ThoiGianTu',
        ]);
        $hocVan->update($validated);
        return response()->json([
            'message' => 'Cập nhật thành công!',
            'data' => $hocVan
        ]);
    }

    public function destroy($id)
    {
        $hocVan = HocVan::findOrFail($id);
        $hocVan->delete();
        return response()->json([
            'message' => 'Xóa học vấn thành công!'
        ]);
    }
}
