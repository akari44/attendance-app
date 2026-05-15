<?php


use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AttendanceController;

use Illuminate\Support\Facades\Route;



// 一般ユーザー
 Route::get('/attendance', [AttendanceController::class, 'show']);


// 管理者ユーザー
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'create']);
    Route::get('/attendance/list', [AdminAttendanceController::class, 'index']);
});