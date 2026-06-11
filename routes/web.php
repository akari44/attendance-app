<?php

use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminRequestApproveController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceListController;
use App\Http\Controllers\StampCorrectionRequestController;
use Illuminate\Support\Facades\Route;



// 一般ユーザー
Route::middleware('auth', 'verified')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'show'])->name('user.attendance');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('user.attendance.store');
    Route::get('/attendance/list', [AttendanceListController::class, 'index'])->name('user.attendance.list');
    Route::get('/attendance/detail/{id}', [AttendanceListController::class, 'show'])->name('user.attendance.detail');
    Route::post('/attendance/detail/{id}', [AttendanceListController::class, 'store'])->name('user.attendance.detail.store');
});

// 管理者ユーザー
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'create'])->name('admin.login');
    Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('admin.logout');
    Route::middleware('auth:admin')->group(function () {
        Route::get('/attendance/list', [AdminAttendanceController::class, 'index'])->name('admin.attendance.list');
        Route::get('/attendance/staff/{id}', [AdminStaffController::class, 'show'])->name('admin.attendance.staff');
        Route::get('/attendance/{id}', [AdminAttendanceController::class, 'show'])->name('admin.attendance.detail');
        Route::put('/attendance/{id}', [AdminAttendanceController::class, 'update'])->name('admin.attendance.detail.update');
        Route::get('/staff/list', [AdminStaffController::class, 'index'])->name('admin.staff.list');
        Route::get('/stamp_correction_request/approve/{id}', [AdminRequestApproveController::class, 'show'])->name('admin.request.approve');
        Route::put('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminRequestApproveController::class, 'update'])->name('admin.request.approve.update');
        Route::get('/attendance/staff/{id}/export', [AdminStaffController::class, 'exportCsv'])->name('admin.staff.export');
    });

});

// 共通
Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'index'])->name('common.request.list')->middleware('auth:web,admin');

