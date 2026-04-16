<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HocVan;

class HocVanController extends Controller
{
    public function index()
    {
        $hocVans = HocVan::all();
        return view('hoc_van.index', compact('hocVans'));
    }

    public function create()
    {
        return view('hoc_van.create');
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
        HocVan::create($validated);
        return redirect()->route('hoc_van.index')->with('success', 'Thêm học vấn thành công!');
    }

    public function edit($id)
    {
        $hocVan = HocVan::findOrFail($id);
        return view('hoc_van.edit', compact('hocVan'));
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
        return redirect()->route('hoc_van.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        $hocVan = HocVan::findOrFail($id);
        $hocVan->delete();
        return redirect()->route('hoc_van.index')->with('success', 'Xóa học vấn thành công!');
    }
}
