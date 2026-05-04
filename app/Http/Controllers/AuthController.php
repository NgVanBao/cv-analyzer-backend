<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'HoTen' => 'required|string|max:255',
            'Email' => 'required|email|unique:NguoiDung,Email',
            'MatKhau' => 'required|string|min:6',
            'Vaitro' => 'required|string|max:50',
        ]);

        $validated['MatKhau'] = Hash::make($validated['MatKhau']);

        $nguoiDung = NguoiDung::create($validated);

        $token = $nguoiDung->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng ký thành công!',
            'data' => $nguoiDung,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'Email' => 'required|email',
            'MatKhau' => 'required',
        ]);

        $user = NguoiDung::where('Email', $request->Email)->first();

        if (!$user || !Hash::check($request->MatKhau, $user->MatKhau)) {
            return response()->json([
                'message' => 'Thông tin đăng nhập không chính xác.'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công!'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
    public function updateEmail(Request $request)
    {
        $request->validate([
            'Email' => 'required|email|unique:NguoiDung,Email,' . $request->user()->MaTaiKhoan . ',MaTaiKhoan',
        ]);

        $user = $request->user();
        $user->Email = $request->Email;
        $user->save();

        return response()->json([
            'message' => 'Cập nhật email thành công!',
            'user' => $user
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->MatKhau)) {
            return response()->json([
                'message' => 'Mật khẩu hiện tại không chính xác.'
            ], 400);
        }

        $user->MatKhau = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Cập nhật mật khẩu thành công!'
        ]);
    }
}
