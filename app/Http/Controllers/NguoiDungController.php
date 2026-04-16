<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Hash;

class NguoiDungController extends Controller
{
    public function index()
    {
        $nguoiDungs = NguoiDung::all();
        return view('nguoi_dung.index', compact('nguoiDungs'));
    }

    public function create()
    {
        return view('nguoi_dung.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'HoTen' => 'required|string|max:255',
            'Email' => 'required|email|unique:nguoi_dung,Email',
            'MatKhau' => 'required|string|min:6',
            'Vaitro' => 'required|string|max:50',
        ]);

        $validated['MatKhau'] = Hash::make($validated['MatKhau']);

        NguoiDung::create($validated);

        return redirect()->route('nguoi_dung.index')->with('success', 'Thêm người dùng thành công!');
    }

    public function edit($id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);
        return view('nguoi_dung.edit', compact('nguoiDung'));
    }

    public function update(Request $request, $id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);

        $validated = $request->validate([
            'HoTen' => 'required|string|max:255',
            'Email' => 'required|email|unique:nguoi_dung,Email,' . $id . ',MaTaiKhoan',
            'MatKhau' => 'nullable|string|min:6',
            'Vaitro' => 'required|string|max:50',
        ]);

        if (!empty($validated['MatKhau'])) {
            $validated['MatKhau'] = Hash::make($validated['MatKhau']);
        } else {
            unset($validated['MatKhau']);
        }

        $nguoiDung->update($validated);

        return redirect()->route('nguoi_dung.index')->with('success', 'Cập nhật tài khoản thành công!');
    }

    public function destroy($id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);
        $nguoiDung->delete();

        return redirect()->route('nguoi_dung.index')->with('success', 'Xóa người dùng thành công!');
    }
}