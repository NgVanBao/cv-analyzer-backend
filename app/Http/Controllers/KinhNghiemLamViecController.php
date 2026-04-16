<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KinhNghiemLamViec;

class KinhNghiemLamViecController extends Controller
{
    public function index()
    {
        $kinhNghiemLamViec = KinhNghiemLamViec::all();
        return view('kinh_nghiem_lam_viec.index', compact('kinhNghiemLamViec'));
    }

    public function create()
    {
        return view('kinh_nghiem_lam_viec.create');
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
        KinhNghiemLamViec::create($validated);
        return redirect()->route('kinh_nghiem_lam_viec.index')->with('success', 'Thêm kinh nghiệm thành công!');
    }

    public function edit($id)
    {
        $kinhNghiemLamViec = KinhNghiemLamViec::findOrFail($id);
        return view('kinh_nghiem_lam_viec.edit', compact('kinhNghiemLamViec'));
    }

    public function update(Request $request, $id)
    {
        $kinhNghiemLamViec = KinhNghiemLamViec::findOrFail($id);
        $validated = $request->validate([
            'MaCV' => 'required|integer|exists:ho_so_cv,MaCV',
            'TenCongTy' => 'required|string|max:255',
            'ViTriCongTac' => 'required|string|max:255',
            'ThoiGianTu' => 'required|date',
            'ThoiGianDen' => 'nullable|date|after_or_equal:ThoiGianTu',
            'MoTaChiTiet' => 'nullable|string',
        ]);
        $kinhNghiemLamViec->update($validated);
        return redirect()->route('kinh_nghiem_lam_viec.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        $kinhNghiemLamViec = KinhNghiemLamViec::findOrFail($id);
        $kinhNghiemLamViec->delete();
        return redirect()->route('kinh_nghiem_lam_viec.index')->with('success', 'Xóa thành công!');
    }
}
