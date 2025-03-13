<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\AuthMiddleware;
use Illuminate\Http\Request;

// Authentication Routes
Route::get('/', function () {
    return view('Auth.login');
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Routes (Protected Routes)
Route::middleware([AuthMiddleware::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Jika menggunakan AuthController
/*
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/forget-password', [AuthController::class, 'forgetPassword'])->name('auth.forget');
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});
*/

// Route untuk menampilkan form lupa password
Route::get('/forgot-password', function () {
    return view('Auth.forget_password');
})->name('password.request');

// Route untuk memproses form lupa password dan redirect ke create password
Route::post('/forgot-password', function (Request $request) {
    // Di sini bisa ditambahkan validasi email jika diperlukan
    return redirect()->route('password.create');
})->name('password.email');

// Route untuk menampilkan form create password
Route::get('/create-password', function () {
    return view('Auth.create_password');
})->name('password.create');

// Route untuk memproses form create password
Route::post('/create-password', function (Request $request) {
    // Logic untuk memproses pembuatan password baru
})->name('password.store');
