<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TuDienKyNang;

class TuDienKyNangController extends Controller
{
    public function index()
    {
        return response()->json(TuDienKyNang::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'TenKyNang' => 'required|string|max:100',
            'LoaiKyNang' => 'required|string|max:50',
        ]);
        $tuDien = TuDienKyNang::create($validated);
        return response()->json([
            'message' => 'Thêm từ điển kỹ năng thành công!',
            'data' => $tuDien
        ], 201);
    }

    public function show($id)
    {
        return response()->json(TuDienKyNang::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $tuDienKyNang = TuDienKyNang::findOrFail($id);
        $validated = $request->validate([
            'TenKyNang' => 'required|string|max:100',
            'LoaiKyNang' => 'required|string|max:50',
        ]);
        $tuDienKyNang->update($validated);
        return response()->json([
            'message' => 'Cập nhật thành công!',
            'data' => $tuDienKyNang
        ]);
    }

    public function destroy($id)
    {
        $tuDienKyNang = TuDienKyNang::findOrFail($id);
        $tuDienKyNang->delete();
        return response()->json([
            'message' => 'Xóa thành công!'
        ]);
    }
}
