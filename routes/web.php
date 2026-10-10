<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DocGiaController;
use App\Http\Controllers\ThuThuController;
use App\Http\Controllers\ThuThuSachController;
use App\Http\Controllers\QuanTriController;

// Web Views
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/index.php', [HomeController::class, 'index']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login.php', [AuthController::class, 'showLogin']);

Route::get('/dangKy.php', [AuthController::class, 'showRegister'])->name('register');
Route::get('/register', [AuthController::class, 'showRegister']);

Route::get('/dangNhapNhanh.php', [AuthController::class, 'showQuickLogin'])->name('quick-login');
Route::get('/quick-login', [AuthController::class, 'showQuickLogin']);
Route::post('/dangNhapNhanh.php', [AuthController::class, 'quickLogin']);
Route::post('/quick-login', [AuthController::class, 'quickLogin']);

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout.php', [AuthController::class, 'logout']);

Route::get('/chiTietTaiLieu.php', [HomeController::class, 'chiTietTaiLieu']);

// APIs (Directly matching existing JavaScript frontend fetch endpoints)
Route::match(['GET', 'POST'], '/api/dangNhap.php', [AuthController::class, 'login']);
Route::match(['GET', 'POST'], '/api/dangKy.php', [AuthController::class, 'register']);

Route::match(['GET', 'POST'], '/api/docGia.php', [DocGiaController::class, 'handle']);
Route::match(['GET', 'POST'], '/api/thuThu.php', [ThuThuController::class, 'handle']);
Route::match(['GET', 'POST'], '/api/thuThuSach.php', [ThuThuSachController::class, 'handle']);
Route::match(['GET', 'POST'], '/api/quanTriTaiKhoan.php', [QuanTriController::class, 'handleTaiKhoan']);
Route::match(['GET', 'POST'], '/api/quanTriHeThong.php', [QuanTriController::class, 'handleHeThong']);