<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KyNangTrongCV;
class KyNangTrongCVController extends Controller
{
    public function index()
    {
        $kyNangTrongCVs = KyNangTrongCV::all();
        return view('ky_nang_trong_cv.index', compact('kyNangTrongCVs'));
    }
    public function create()
    {
        return view('ky_nang_trong_cv.create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'MaKyNang' => 'required|integer|exists:tu_dien_ky_nang,MaKyNang',
            'MaCV' => 'required|integer|exists:ho_so_cv,MaCV',
        ]);
        KyNangTrongCV::create($validated);
        return redirect()->route('ky_nang_trong_cv.index');
    }
    public function edit($id)
    {
        $kyNangTrongCV = KyNangTrongCV::findOrFail($id);
        return view('ky_nang_trong_cv.edit', compact('kyNangTrongCV'));
    }
    public function update(Request $request, $id)
    {
        $kyNangTrongCV = KyNangTrongCV::findOrFail($id);
        $validated = $request->validate([
            'MaKyNang' => 'required|integer|exists:tu_dien_ky_nang,MaKyNang',
            'MaCV' => 'required|integer|exists:ho_so_cv,MaCV',
        ]);
        $kyNangTrongCV->update($validated);
        return redirect()->route('ky_nang_trong_cv.index');
    }
    public function destroy($id)
    {
        $kyNangTrongCV = KyNangTrongCV::findOrFail($id);
        $kyNangTrongCV->delete();
        return redirect()->route('ky_nang_trong_cv.index');
    }
}
