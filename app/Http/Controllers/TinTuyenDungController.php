<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TinTuyenDung;

class TinTuyenDungController extends Controller
{
    public function index()
    {
        return response()->json(TinTuyenDung::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'TieuDe' => 'required|string|max:255',
            'TenCongTy' => 'required|string|max:255',
            'MoTaChiTiet' => 'required|string',
            'LuongToiThieu' => 'nullable|integer|min:0',
            'LuongToiDa' => 'nullable|integer|gte:LuongToiThieu',
            'TrangThai' => 'required|string|max:50',
            'DiaDiem' => 'nullable|string|max:255',
            'NgayDangTuyen' => 'nullable|date',
            'HanNop' => 'nullable|date',
            'LoaiHinh' => 'nullable|string|max:100',
            'CapBac' => 'nullable|string|max:100',
        ]);
        $tinTuyenDung = TinTuyenDung::create($validated);
        return response()->json([
            'message' => 'Thêm tin tuyển dụng thành công!',
            'data' => $tinTuyenDung
        ], 201);
    }

    public function show($id)
    {
        return response()->json(TinTuyenDung::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $tinTuyenDung = TinTuyenDung::findOrFail($id);
        $validated = $request->validate([
            'TieuDe' => 'required|string|max:255',
            'TenCongTy' => 'required|string|max:255',
            'MoTaChiTiet' => 'required|string',
            'LuongToiThieu' => 'nullable|integer|min:0',
            'LuongToiDa' => 'nullable|integer|gte:LuongToiThieu',
            'TrangThai' => 'required|string|max:50',
            'DiaDiem' => 'nullable|string|max:255',
            'NgayDangTuyen' => 'nullable|date',
            'HanNop' => 'nullable|date',
            'LoaiHinh' => 'nullable|string|max:100',
            'CapBac' => 'nullable|string|max:100',
        ]);
        $tinTuyenDung->update($validated);
        return response()->json([
            'message' => 'Cập nhật thành công!',
            'data' => $tinTuyenDung
        ]);
    }

    public function destroy($id)
    {
        $tinTuyenDung = TinTuyenDung::findOrFail($id);
        $tinTuyenDung->delete();
        return response()->json([
            'message' => 'Xóa thành công!'
        ]);
    }
}
