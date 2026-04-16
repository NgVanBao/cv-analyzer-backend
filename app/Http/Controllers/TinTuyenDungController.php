<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TinTuyenDung;

class TinTuyenDungController extends Controller
{
    public function index()
    {
        $tinTuyenDungs = TinTuyenDung::all();
        return view('tin_tuyen_dung.index', compact('tinTuyenDungs'));
    }

    public function create()
    {
        return view('tin_tuyen_dung.create');
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
        ]);
        TinTuyenDung::create($validated);
        return redirect()->route('tin_tuyen_dung.index')->with('success', 'Thêm tin tuyển dụng thành công!');
    }

    public function edit($id)
    {
        $tinTuyenDung = TinTuyenDung::findOrFail($id);
        return view('tin_tuyen_dung.edit', compact('tinTuyenDung'));
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
        ]);
        $tinTuyenDung->update($validated);
        return redirect()->route('tin_tuyen_dung.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        $tinTuyenDung = TinTuyenDung::findOrFail($id);
        $tinTuyenDung->delete();
        return redirect()->route('tin_tuyen_dung.index')->with('success', 'Xóa thành công!');
    }
}
