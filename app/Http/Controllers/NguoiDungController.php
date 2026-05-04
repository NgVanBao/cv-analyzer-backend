<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Hash;

class NguoiDungController extends Controller
{
    public function index()
    {
        return response()->json(NguoiDung::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'HoTen' => 'required|string|max:255',
            'Email' => 'required|email|unique:NguoiDung,Email',
            'MatKhau' => 'required|string|min:6',
            'Vaitro' => 'required|string|max:50',
        ]);

        $validated['MatKhau'] = Hash::make($validated['MatKhau']);

        $nguoiDung = NguoiDung::create($validated);

        return response()->json([
            'message' => 'Thêm người dùng thành công!',
            'data' => $nguoiDung
        ], 201);
    }

    public function show($id)
    {
        return response()->json(NguoiDung::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);

        $validated = $request->validate([
            'HoTen' => 'required|string|max:255',
            'Email' => 'required|email|unique:NguoiDung,Email,' . $id . ',MaTaiKhoan',
            'MatKhau' => 'nullable|string|min:6',
            'Vaitro' => 'required|string|max:50',
        ]);

        if (!empty($validated['MatKhau'])) {
            $validated['MatKhau'] = Hash::make($validated['MatKhau']);
        } else {
            unset($validated['MatKhau']);
        }

        $nguoiDung->update($validated);

        return response()->json([
            'message' => 'Cập nhật tài khoản thành công!',
            'data' => $nguoiDung
        ]);
    }

    public function destroy($id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);
        $nguoiDung->delete();

        return response()->json([
            'message' => 'Xóa người dùng thành công!'
        ]);
    }
}