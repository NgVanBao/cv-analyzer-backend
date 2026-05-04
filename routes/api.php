<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AILogController;
use App\Http\Controllers\HoSoCVController;
use App\Http\Controllers\HocVanController;
use App\Http\Controllers\KetQuaGoiYController;
use App\Http\Controllers\KinhNghiemLamViecController;
use App\Http\Controllers\KyNangTrongCVController;
use App\Http\Controllers\KyNangYeuCauController;
use App\Http\Controllers\NguoiDungController;
use App\Http\Controllers\TinTuyenDungController;
use App\Http\Controllers\TuDienKyNangController;
use App\Http\Controllers\AuthController;



// XÁC THỰC (Authentication)
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::put('update-email', [AuthController::class, 'updateEmail']);
    Route::put('update-password', [AuthController::class, 'updatePassword']);
});


Route::get('nguoi_dung', [NguoiDungController::class, 'index']);
Route::post('nguoi_dung', [NguoiDungController::class, 'store']);
Route::get('nguoi_dung/{id}', [NguoiDungController::class, 'show']);
Route::put('nguoi_dung/{id}', [NguoiDungController::class, 'update']);
Route::delete('nguoi_dung/{id}', [NguoiDungController::class, 'destroy']);


Route::get('ho_so_cv', [HoSoCVController::class, 'index']);
Route::post('ho_so_cv', [HoSoCVController::class, 'store']);
Route::get('ho_so_cv/{id}', [HoSoCVController::class, 'show']);
Route::put('ho_so_cv/{id}', [HoSoCVController::class, 'update']);
Route::delete('ho_so_cv/{id}', [HoSoCVController::class, 'destroy']);


Route::get('tin_tuyen_dung', [TinTuyenDungController::class, 'index']);
Route::post('tin_tuyen_dung', [TinTuyenDungController::class, 'store']);
Route::get('tin_tuyen_dung/{id}', [TinTuyenDungController::class, 'show']);
Route::put('tin_tuyen_dung/{id}', [TinTuyenDungController::class, 'update']);
Route::delete('tin_tuyen_dung/{id}', [TinTuyenDungController::class, 'destroy']);


Route::get('hoc_van', [HocVanController::class, 'index']);
Route::post('hoc_van', [HocVanController::class, 'store']);
Route::get('hoc_van/{id}', [HocVanController::class, 'show']);
Route::put('hoc_van/{id}', [HocVanController::class, 'update']);
Route::delete('hoc_van/{id}', [HocVanController::class, 'destroy']);


Route::get('kinh_nghiem_lam_viec', [KinhNghiemLamViecController::class, 'index']);
Route::post('kinh_nghiem_lam_viec', [KinhNghiemLamViecController::class, 'store']);
Route::get('kinh_nghiem_lam_viec/{id}', [KinhNghiemLamViecController::class, 'show']);
Route::put('kinh_nghiem_lam_viec/{id}', [KinhNghiemLamViecController::class, 'update']);
Route::delete('kinh_nghiem_lam_viec/{id}', [KinhNghiemLamViecController::class, 'destroy']);


Route::get('tu_dien_ky_nang', [TuDienKyNangController::class, 'index']);
Route::post('tu_dien_ky_nang', [TuDienKyNangController::class, 'store']);
Route::get('tu_dien_ky_nang/{id}', [TuDienKyNangController::class, 'show']);
Route::put('tu_dien_ky_nang/{id}', [TuDienKyNangController::class, 'update']);
Route::delete('tu_dien_ky_nang/{id}', [TuDienKyNangController::class, 'destroy']);

Route::get('ky_nang_trong_cv', [KyNangTrongCVController::class, 'index']);
Route::post('ky_nang_trong_cv', [KyNangTrongCVController::class, 'store']);
Route::get('ky_nang_trong_cv/{id}', [KyNangTrongCVController::class, 'show']);
Route::put('ky_nang_trong_cv/{id}', [KyNangTrongCVController::class, 'update']);
Route::delete('ky_nang_trong_cv/{id}', [KyNangTrongCVController::class, 'destroy']);


Route::get('ky_nang_yeu_cau', [KyNangYeuCauController::class, 'index']);
Route::post('ky_nang_yeu_cau', [KyNangYeuCauController::class, 'store']);
Route::get('ky_nang_yeu_cau/{id}', [KyNangYeuCauController::class, 'show']);
Route::put('ky_nang_yeu_cau/{id}', [KyNangYeuCauController::class, 'update']);
Route::delete('ky_nang_yeu_cau/{id}', [KyNangYeuCauController::class, 'destroy']);


Route::get('ket_qua_goi_y', [KetQuaGoiYController::class, 'index']);
Route::post('ket_qua_goi_y', [KetQuaGoiYController::class, 'store']);
Route::get('ket_qua_goi_y/{id}', [KetQuaGoiYController::class, 'show']);
Route::put('ket_qua_goi_y/{id}', [KetQuaGoiYController::class, 'update']);
Route::delete('ket_qua_goi_y/{id}', [KetQuaGoiYController::class, 'destroy']);


Route::get('ai_log', [AILogController::class, 'index']);
Route::post('ai_log', [AILogController::class, 'store']);
Route::get('ai_log/{id}', [AILogController::class, 'show']);
Route::put('ai_log/{id}', [AILogController::class, 'update']);
Route::delete('ai_log/{id}', [AILogController::class, 'destroy']);
