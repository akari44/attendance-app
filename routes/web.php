<?php

use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminRequestApproveController;

use Illuminate\Support\Facades\Route;



// 一般ユーザー


// 管理者ユーザー
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'create'])->name('admin.login');
    Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('admin.logout');
    Route::middleware('auth')->group(function () {
        Route::get('/attendance/list', [AdminAttendanceController::class, 'index'])->name('admin.attendance.list');
        //Route::get('/attendance/{id}', [AdminAttendanceController::class, 'show'])->name('admin.attendance.detail');
        Route::get('/staff/list', [AdminStaffController::class, 'index'])->name('admin.staff.list');
        Route::get('/attendance/staff/{id}', [AdminStaffController::class, 'show'])->name('admin.attendance.staff');
        Route::get('/stamp_correction_request/list', [AdminRequestApproveController::class, 'index'])->name('admin.request.list');
        // 今後増える管理者ルートもここに
    });

});