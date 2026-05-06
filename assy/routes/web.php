<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StockTransactionController;
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
    // Dashboard - All authenticated users
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Transactions - All authenticated users can create
    Route::resource('transactions', StockTransactionController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    Route::post('transactions-export', [StockTransactionController::class, 'export'])->name('transactions.export');
    Route::post('transactions/{transaction}/confirm', [StockTransactionController::class, 'confirm'])->name('transactions.confirm');
    Route::post('transactions/{transaction}/cancel', [StockTransactionController::class, 'cancel'])->name('transactions.cancel');

    // Profile
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Allow all authenticated users to view sparepart list and detail
    Route::get('spareparts', [SparepartController::class, 'index'])->name('spareparts.index');
    Route::get('spareparts/{sparepart}', [SparepartController::class, 'show'])->name('spareparts.show');

    // Admin Only Routes
    Route::middleware('admin')->group(function () {
        Route::resource('spareparts', SparepartController::class)->except(['index', 'show']);
        Route::get('spareparts-export', [SparepartController::class, 'exportExcel'])->name('spareparts.export');
        Route::post('spareparts-import', [SparepartController::class, 'importExcel'])->name('spareparts.import');
        Route::resource('categories', CategoryController::class);
        Route::resource('users', UserController::class);
    });
});
