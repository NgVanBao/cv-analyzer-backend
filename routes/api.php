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


Route::get('nguoi-dung', [NguoiDungController::class, 'index']);
Route::post('nguoi-dung', [NguoiDungController::class, 'store']);
Route::get('nguoi-dung/{id}', [NguoiDungController::class, 'show']);
Route::put('nguoi-dung/{id}', [NguoiDungController::class, 'update']);
Route::delete('nguoi-dung/{id}', [NguoiDungController::class, 'destroy']);


Route::get('ho-so-cv', [HoSoCVController::class, 'index']);
Route::post('ho-so-cv', [HoSoCVController::class, 'store']);
Route::get('ho-so-cv/{id}', [HoSoCVController::class, 'show']);
Route::put('ho-so-cv/{id}', [HoSoCVController::class, 'update']);
Route::delete('ho-so-cv/{id}', [HoSoCVController::class, 'destroy']);


Route::get('tin-tuyen-dung', [TinTuyenDungController::class, 'index']);
Route::post('tin-tuyen-dung', [TinTuyenDungController::class, 'store']);
Route::get('tin-tuyen-dung/{id}', [TinTuyenDungController::class, 'show']);
Route::put('tin-tuyen-dung/{id}', [TinTuyenDungController::class, 'update']);
Route::delete('tin-tuyen-dung/{id}', [TinTuyenDungController::class, 'destroy']);


Route::get('hoc-van', [HocVanController::class, 'index']);
Route::post('hoc-van', [HocVanController::class, 'store']);
Route::get('hoc-van/{id}', [HocVanController::class, 'show']);
Route::put('hoc-van/{id}', [HocVanController::class, 'update']);
Route::delete('hoc-van/{id}', [HocVanController::class, 'destroy']);


Route::get('kinh-nghiem-lam-viec', [KinhNghiemLamViecController::class, 'index']);
Route::post('kinh-nghiem-lam-viec', [KinhNghiemLamViecController::class, 'store']);
Route::get('kinh-nghiem-lam-viec/{id}', [KinhNghiemLamViecController::class, 'show']);
Route::put('kinh-nghiem-lam-viec/{id}', [KinhNghiemLamViecController::class, 'update']);
Route::delete('kinh-nghiem-lam-viec/{id}', [KinhNghiemLamViecController::class, 'destroy']);


Route::get('tu-dien-ky-nang', [TuDienKyNangController::class, 'index']);
Route::post('tu-dien-ky-nang', [TuDienKyNangController::class, 'store']);
Route::get('tu-dien-ky-nang/{id}', [TuDienKyNangController::class, 'show']);
Route::put('tu-dien-ky-nang/{id}', [TuDienKyNangController::class, 'update']);
Route::delete('tu-dien-ky-nang/{id}', [TuDienKyNangController::class, 'destroy']);

Route::get('ky-nang-trong-cv', [KyNangTrongCVController::class, 'index']);
Route::post('ky-nang-trong-cv', [KyNangTrongCVController::class, 'store']);
Route::get('ky-nang-trong-cv/{id}', [KyNangTrongCVController::class, 'show']);
Route::put('ky-nang-trong-cv/{id}', [KyNangTrongCVController::class, 'update']);
Route::delete('ky-nang-trong-cv/{id}', [KyNangTrongCVController::class, 'destroy']);


Route::get('ky-nang-yeu-cau', [KyNangYeuCauController::class, 'index']);
Route::post('ky-nang-yeu-cau', [KyNangYeuCauController::class, 'store']);
Route::get('ky-nang-yeu-cau/{id}', [KyNangYeuCauController::class, 'show']);
Route::put('ky-nang-yeu-cau/{id}', [KyNangYeuCauController::class, 'update']);
Route::delete('ky-nang-yeu-cau/{id}', [KyNangYeuCauController::class, 'destroy']);


Route::get('ket-qua-goi-y', [KetQuaGoiYController::class, 'index']);
Route::post('ket-qua-goi-y', [KetQuaGoiYController::class, 'store']);
Route::get('ket-qua-goi-y/{id}', [KetQuaGoiYController::class, 'show']);
Route::put('ket-qua-goi-y/{id}', [KetQuaGoiYController::class, 'update']);
Route::delete('ket-qua-goi-y/{id}', [KetQuaGoiYController::class, 'destroy']);


Route::get('ai-log', [AILogController::class, 'index']);
Route::post('ai-log', [AILogController::class, 'store']);
Route::get('ai-log/{id}', [AILogController::class, 'show']);
Route::put('ai-log/{id}', [AILogController::class, 'update']);
Route::delete('ai-log/{id}', [AILogController::class, 'destroy']);
