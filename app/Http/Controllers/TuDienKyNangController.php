<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TuDienKyNang;

class TuDienKyNangController extends Controller
{
    public function index()
    {
        $tuDienKyNangs = TuDienKyNang::all();
        return view('tu_dien_ky_nang.index', compact('tuDienKyNangs'));
    }

    public function create()
    {
        return view('tu_dien_ky_nang.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'TenKyNang' => 'required|string|max:100',
            'LoaiKyNang' => 'required|string|max:50',
        ]);
        TuDienKyNang::create($validated);
        return redirect()->route('tu_dien_ky_nang.index')->with('success', 'Thêm từ điển kỹ năng thành công!');
    }

    public function edit($id)
    {
        $tuDienKyNang = TuDienKyNang::findOrFail($id);
        return view('tu_dien_ky_nang.edit', compact('tuDienKyNang'));
    }

    public function update(Request $request, $id)
    {
        $tuDienKyNang = TuDienKyNang::findOrFail($id);
        $validated = $request->validate([
            'TenKyNang' => 'required|string|max:100',
            'LoaiKyNang' => 'required|string|max:50',
        ]);
        $tuDienKyNang->update($validated);
        return redirect()->route('tu_dien_ky_nang.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        $tuDienKyNang = TuDienKyNang::findOrFail($id);
        $tuDienKyNang->delete();
        return redirect()->route('tu_dien_ky_nang.index')->with('success', 'Xóa thành công!');
    }
}
