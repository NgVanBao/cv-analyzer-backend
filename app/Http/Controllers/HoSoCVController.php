<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HoSoCV;

class HoSoCVController extends Controller
{
    public function index()
    {
        $hoSoCVs = HoSoCV::all();
        return view('ho_so_cv.index', compact('hoSoCVs'));
    }
    public function create()
    {
        return view('ho_so_cv.create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'MaTaiKhoan' => 'required|integer|exists:nguoi_dung,MaTaiKhoan',
            'TenFile' => 'required|string|max:255',
            'DuongDanFile' => 'required|string|max:255',
            'DuLieuAITrichXuat' => 'nullable|string',
            'TrangThaiXuLy' => 'required|string|max:50',
            'TrinhDoHocVan' => 'nullable|string|max:100',
            'KinhNghiem' => 'nullable|string',
            'KyNang' => 'nullable|string',
        ]);
        HoSoCV::create($validated);
        return redirect()->route('ho_so_cv.index');
    }
    public function edit($id)
    {
        $hoSoCV = HoSoCV::findOrFail($id);
        return view('ho_so_cv.edit', compact('hoSoCV'));
    }
    public function update(Request $request, $id)
    {
        $hoSoCV = HoSoCV::findOrFail($id);
        $validated = $request->validate([
            'MaTaiKhoan' => 'required|integer|exists:nguoi_dung,MaTaiKhoan',
            'TenFile' => 'required|string|max:255',
            'DuongDanFile' => 'required|string|max:255',
            'DuLieuAITrichXuat' => 'nullable|string',
            'TrangThaiXuLy' => 'required|string|max:50',
            'TrinhDoHocVan' => 'nullable|string|max:100',
            'KinhNghiem' => 'nullable|string',
            'KyNang' => 'nullable|string',
        ]);
        $hoSoCV->update($validated);
        return redirect()->route('ho_so_cv.index');
    }
    public function destroy($id)
    {
        $hoSoCV = HoSoCV::findOrFail($id);
        $hoSoCV->delete();
        return redirect()->route('ho_so_cv.index');
    }
}
