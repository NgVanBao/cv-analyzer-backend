<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KyNangYeuCau;

class KyNangYeuCauController extends Controller
{
    public function index()
    {
        $kyNangYeuCaus = KyNangYeuCau::all();
        return view('ky_nang_yeu_cau.index', compact('kyNangYeuCaus'));
    }

    public function create()
    {
        return view('ky_nang_yeu_cau.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'MaKyNang' => 'required|integer|exists:tu_dien_ky_nang,MaKyNang',
            'MaTuyenDung' => 'required|integer|exists:tin_tuyen_dung,MaTuyenDung',
            'TrongSoDiem' => 'nullable|integer|min:0',
        ]);
        KyNangYeuCau::create($validated);
        return redirect()->route('ky_nang_yeu_cau.index')->with('success', 'Thêm kỹ năng yêu cầu thành công!');
    }

    public function edit($id)
    {
        $kyNangYeuCau = KyNangYeuCau::findOrFail($id);
        return view('ky_nang_yeu_cau.edit', compact('kyNangYeuCau'));
    }

    public function update(Request $request, $id)
    {
        $kyNangYeuCau = KyNangYeuCau::findOrFail($id);
        $validated = $request->validate([
            'MaKyNang' => 'required|integer|exists:tu_dien_ky_nang,MaKyNang',
            'MaTuyenDung' => 'required|integer|exists:tin_tuyen_dung,MaTuyenDung',
            'TrongSoDiem' => 'nullable|integer|min:0',
        ]);
        $kyNangYeuCau->update($validated);
        return redirect()->route('ky_nang_yeu_cau.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        $kyNangYeuCau = KyNangYeuCau::findOrFail($id);
        $kyNangYeuCau->delete();
        return redirect()->route('ky_nang_yeu_cau.index')->with('success', 'Xóa thành công!');
    }
}
