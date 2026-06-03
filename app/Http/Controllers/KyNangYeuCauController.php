<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KyNangYeuCau;

class KyNangYeuCauController extends Controller
{
    public function index()
    {
        return response()->json(KyNangYeuCau::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'MaKyNang' => 'required|integer|exists:TuDienKyNang,MaKyNang',
            'MaTuyenDung' => 'required|integer|exists:TinTuyenDung,MaTuyenDung',
            'TrongSoDiem' => 'nullable|integer|min:0',
        ]);
        $kyNangYeuCau = KyNangYeuCau::create($validated);
        return response()->json([
            'message' => 'Thêm kỹ năng yêu cầu thành công!',
            'data' => $kyNangYeuCau
        ], 201);
    }

    public function show($id)
    {
        return response()->json(KyNangYeuCau::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $kyNangYeuCau = KyNangYeuCau::findOrFail($id);
        $validated = $request->validate([
            'MaKyNang' => 'required|integer|exists:TuDienKyNang,MaKyNang',
            'MaTuyenDung' => 'required|integer|exists:TinTuyenDung,MaTuyenDung',
            'TrongSoDiem' => 'nullable|integer|min:0',
        ]);
        $kyNangYeuCau->update($validated);
        return response()->json([
            'message' => 'Cập nhật thành công!',
            'data' => $kyNangYeuCau
        ]);
    }

    public function destroy($id)
    {
        $kyNangYeuCau = KyNangYeuCau::findOrFail($id);
        $kyNangYeuCau->delete();
        return response()->json([
            'message' => 'Xóa thành công!'
        ]);
    }
}
