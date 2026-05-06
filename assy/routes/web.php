<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Auth Routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes (Authenticated Users)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Admin Only Routes
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
    });
});
