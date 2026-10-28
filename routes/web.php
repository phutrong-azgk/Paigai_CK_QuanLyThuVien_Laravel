<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DocGiaController;
use App\Http\Controllers\ThuThuController;
use App\Http\Controllers\ThuThuSachController;
use App\Http\Controllers\QuanTriController;

// Web Views
Route::middleware('role')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/index.php', [HomeController::class, 'index']);
    Route::get('/chiTietTaiLieu.php', [HomeController::class, 'chiTietTaiLieu']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/logout.php', [AuthController::class, 'logout']);
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login.php', [AuthController::class, 'showLogin']);

Route::get('/dangKy.php', [AuthController::class, 'showRegister'])->name('register');
Route::get('/register', [AuthController::class, 'showRegister']);

Route::get('/dangNhapNhanh.php', [AuthController::class, 'showQuickLogin'])->name('quick-login');
Route::get('/quick-login', [AuthController::class, 'showQuickLogin']);
Route::post('/dangNhapNhanh.php', [AuthController::class, 'quickLogin'])->middleware('throttle:10,1');
Route::post('/quick-login', [AuthController::class, 'quickLogin'])->middleware('throttle:10,1');

// APIs (Directly matching existing JavaScript frontend fetch endpoints)
Route::post('/api/dangNhap.php', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/api/dangKy.php', [AuthController::class, 'register'])->middleware('throttle:3,1');

Route::match(['GET', 'POST'], '/api/docGia.php', [DocGiaController::class, 'handle'])->middleware('role:reader');
Route::match(['GET', 'POST'], '/api/thuThu.php', [ThuThuController::class, 'handle'])->middleware('role:librarian');
Route::match(['GET', 'POST'], '/api/thuThuSach.php', [ThuThuSachController::class, 'handle'])->middleware('role:librarian');
Route::match(['GET', 'POST'], '/api/quanTriTaiKhoan.php', [QuanTriController::class, 'handleTaiKhoan'])->middleware('role:admin');
Route::match(['GET', 'POST'], '/api/quanTriHeThong.php', [QuanTriController::class, 'handleHeThong'])->middleware('role:admin');
