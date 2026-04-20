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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// XÁC THỰC (Authentication)
Route::post('login', [NguoiDungController::class, 'login']);
Route::post('register', [NguoiDungController::class, 'store']); // Dùng chung với store người dùng

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [NguoiDungController::class, 'logout']);
    Route::get('me', [NguoiDungController::class, 'me']);
});

// 1. NGƯỜI DÙNG (Users)
Route::get('nguoi-dung', [NguoiDungController::class, 'index']);
Route::post('nguoi-dung', [NguoiDungController::class, 'store']);
Route::get('nguoi-dung/{id}', [NguoiDungController::class, 'show']);
Route::put('nguoi-dung/{id}', [NguoiDungController::class, 'update']);
Route::delete('nguoi-dung/{id}', [NguoiDungController::class, 'destroy']);

// 2. HỒ SƠ CV (Resume)
Route::get('ho-so-cv', [HoSoCVController::class, 'index']);
Route::post('ho-so-cv', [HoSoCVController::class, 'store']);
Route::get('ho-so-cv/{id}', [HoSoCVController::class, 'show']);
Route::put('ho-so-cv/{id}', [HoSoCVController::class, 'update']);
Route::delete('ho-so-cv/{id}', [HoSoCVController::class, 'destroy']);

// 3. TIN TUYỂN DỤNG (Job Posts)
Route::get('tin-tuyen-dung', [TinTuyenDungController::class, 'index']);
Route::post('tin-tuyen-dung', [TinTuyenDungController::class, 'store']);
Route::get('tin-tuyen-dung/{id}', [TinTuyenDungController::class, 'show']);
Route::put('tin-tuyen-dung/{id}', [TinTuyenDungController::class, 'update']);
Route::delete('tin-tuyen-dung/{id}', [TinTuyenDungController::class, 'destroy']);

// 4. HỌC VẤN (Education)
Route::get('hoc-van', [HocVanController::class, 'index']);
Route::post('hoc-van', [HocVanController::class, 'store']);
Route::get('hoc-van/{id}', [HocVanController::class, 'show']);
Route::put('hoc-van/{id}', [HocVanController::class, 'update']);
Route::delete('hoc-van/{id}', [HocVanController::class, 'destroy']);

// 5. KINH NGHIỆM LÀM VIỆC (Experience)
Route::get('kinh-nghiem-lam-viec', [KinhNghiemLamViecController::class, 'index']);
Route::post('kinh-nghiem-lam-viec', [KinhNghiemLamViecController::class, 'store']);
Route::get('kinh-nghiem-lam-viec/{id}', [KinhNghiemLamViecController::class, 'show']);
Route::put('kinh-nghiem-lam-viec/{id}', [KinhNghiemLamViecController::class, 'update']);
Route::delete('kinh-nghiem-lam-viec/{id}', [KinhNghiemLamViecController::class, 'destroy']);

// 6. TỪ ĐIỂN KỸ NĂNG (Skills Dictionary)
Route::get('tu-dien-ky-nang', [TuDienKyNangController::class, 'index']);
Route::post('tu-dien-ky-nang', [TuDienKyNangController::class, 'store']);
Route::get('tu-dien-ky-nang/{id}', [TuDienKyNangController::class, 'show']);
Route::put('tu-dien-ky-nang/{id}', [TuDienKyNangController::class, 'update']);
Route::delete('tu-dien-ky-nang/{id}', [TuDienKyNangController::class, 'destroy']);

// 7. KỸ NĂNG TRONG CV (CV Skills)
Route::get('ky-nang-trong-cv', [KyNangTrongCVController::class, 'index']);
Route::post('ky-nang-trong-cv', [KyNangTrongCVController::class, 'store']);
Route::get('ky-nang-trong-cv/{id}', [KyNangTrongCVController::class, 'show']);
Route::put('ky-nang-trong-cv/{id}', [KyNangTrongCVController::class, 'update']);
Route::delete('ky-nang-trong-cv/{id}', [KyNangTrongCVController::class, 'destroy']);

// 8. KỸ NĂNG YÊU CẦU (Requirement Skills)
Route::get('ky-nang-yeu-cau', [KyNangYeuCauController::class, 'index']);
Route::post('ky-nang-yeu-cau', [KyNangYeuCauController::class, 'store']);
Route::get('ky-nang-yeu-cau/{id}', [KyNangYeuCauController::class, 'show']);
Route::put('ky-nang-yeu-cau/{id}', [KyNangYeuCauController::class, 'update']);
Route::delete('ky-nang-yeu-cau/{id}', [KyNangYeuCauController::class, 'destroy']);

// 9. KẾT QUẢ GỢI Ý (Suggestion Results)
Route::get('ket-qua-goi-y', [KetQuaGoiYController::class, 'index']);
Route::post('ket-qua-goi-y', [KetQuaGoiYController::class, 'store']);
Route::get('ket-qua-goi-y/{id}', [KetQuaGoiYController::class, 'show']);
Route::put('ket-qua-goi-y/{id}', [KetQuaGoiYController::class, 'update']);
Route::delete('ket-qua-goi-y/{id}', [KetQuaGoiYController::class, 'destroy']);

// 10. AI LOGS
Route::get('ai-logs', [AILogController::class, 'index']);
Route::post('ai-logs', [AILogController::class, 'store']);
Route::get('ai-logs/{id}', [AILogController::class, 'show']);
Route::put('ai-logs/{id}', [AILogController::class, 'update']);
Route::delete('ai-logs/{id}', [AILogController::class, 'destroy']);
