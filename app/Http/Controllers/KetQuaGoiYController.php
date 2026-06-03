<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KetQuaGoiY;

class KetQuaGoiYController extends Controller
{
    public function index()
    {
        return response()->json(KetQuaGoiY::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'MaCV' => 'required|integer|exists:HoSoCV,MaCV',
            'MaTuyenDung' => 'required|integer|exists:TinTuyenDung,MaTuyenDung',
            'TyLePhuHop' => 'required|numeric|min:0|max:100',
        ]);
        $ketQuaGoiY = KetQuaGoiY::create($validated);
        return response()->json([
            'message' => 'Thêm kết quả gợi ý thành công!',
            'data' => $ketQuaGoiY
        ], 201);
    }

    public function show($id)
    {
        return response()->json(KetQuaGoiY::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $ketQuaGoiY = KetQuaGoiY::findOrFail($id);
        $validated = $request->validate([
            'MaCV' => 'required|integer|exists:HoSoCV,MaCV',
            'MaTuyenDung' => 'required|integer|exists:TinTuyenDung,MaTuyenDung',
            'TyLePhuHop' => 'required|numeric|min:0|max:100',
        ]);
        $ketQuaGoiY->update($validated);
        return response()->json([
            'message' => 'Cập nhật thành công!',
            'data' => $ketQuaGoiY
        ]);
    }

    public function destroy($id)
    {
        $ketQuaGoiY = KetQuaGoiY::findOrFail($id);
        $ketQuaGoiY->delete();
        return response()->json([
            'message' => 'Xóa thành công!'
        ]);
    }

    /**
     * Lấy danh sách gợi ý việc làm cho một CV cụ thể kèm thông tin công việc
     */
    public function getByCV($cvId)
    {
        $results = KetQuaGoiY::with('tinTuyenDung')
            ->where('MaCV', $cvId)
            ->orderBy('TyLePhuHop', 'desc')
            ->get();

        return response()->json($results);
    }
}