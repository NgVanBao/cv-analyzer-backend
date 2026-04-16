<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KetQuaGoiY;

class KetQuaGoiYController extends Controller
{
    public function index()
    {
        $ketQuaGoiYs = KetQuaGoiY::all();
        return view('ket_qua_goi_y.index', compact('ketQuaGoiYs'));
    }

    public function create()
    {
        return view('ket_qua_goi_y.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'MaCV' => 'required|integer|exists:ho_so_cv,MaCV',
            'MaTuyenDung' => 'required|integer|exists:tin_tuyen_dung,MaTuyenDung',
            'TyLePhuHop' => 'required|numeric|min:0|max:100',
        ]);
        KetQuaGoiY::create($validated);
        return redirect()->route('ket_qua_goi_y.index')->with('success', 'Thêm kết quả gợi ý thành công!');
    }

    public function edit($id)
    {
        $ketQuaGoiY = KetQuaGoiY::findOrFail($id);
        return view('ket_qua_goi_y.edit', compact('ketQuaGoiY'));
    }

    public function update(Request $request, $id)
    {
        $ketQuaGoiY = KetQuaGoiY::findOrFail($id);
        $validated = $request->validate([
            'MaCV' => 'required|integer|exists:ho_so_cv,MaCV',
            'MaTuyenDung' => 'required|integer|exists:tin_tuyen_dung,MaTuyenDung',
            'TyLePhuHop' => 'required|numeric|min:0|max:100',
        ]);
        $ketQuaGoiY->update($validated);
        return redirect()->route('ket_qua_goi_y.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        $ketQuaGoiY = KetQuaGoiY::findOrFail($id);
        $ketQuaGoiY->delete();
        return redirect()->route('ket_qua_goi_y.index')->with('success', 'Xóa thành công!');
    }
}