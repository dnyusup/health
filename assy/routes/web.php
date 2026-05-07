<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AssyPartController;
use App\Http\Controllers\AssyMachineController;
use App\Http\Controllers\AssyWorkOrderController;
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

    // Parts - semua user bisa full CRUD
    Route::resource('parts', AssyPartController::class);
    Route::get('parts-export', [AssyPartController::class, 'exportExcel'])->name('parts.export');
    Route::post('parts-import', [AssyPartController::class, 'importExcel'])->name('parts.import');

    // Machines - semua user bisa full CRUD
    Route::resource('machines', AssyMachineController::class);
    Route::get('machines-export', [AssyMachineController::class, 'exportExcel'])->name('machines.export');
    Route::post('machines-import', [AssyMachineController::class, 'importExcel'])->name('machines.import');

    // Work Orders
    Route::resource('work-orders', AssyWorkOrderController::class);
    Route::get('work-orders-export', [AssyWorkOrderController::class, 'exportExcel'])->name('work-orders.export');
    Route::post('work-orders/{work_order}/repair', [AssyWorkOrderController::class, 'repair'])->name('work-orders.repair');
    Route::get('api/part-lookup', [AssyWorkOrderController::class, 'partLookup'])->name('api.part-lookup');

    // Admin Only Routes
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
    });
});
