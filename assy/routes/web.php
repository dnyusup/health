<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AssyPartController;
use App\Http\Controllers\AssyMachineController;
use App\Http\Controllers\AssyWorkOrderController;
use App\Http\Controllers\ReadyStockController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Storage file serve via /files/ prefix (avoids Apache-level 403 on /storage/ symlink)
Route::get('files/{path}', function (string $path) {
    $disk = Storage::disk('public');
    if (! $disk->exists($path)) {
        abort(404);
    }
    return response()->file($disk->path($path));
})->where('path', '.*')->name('storage.serve');

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
    Route::get('work-orders-import-template', [AssyWorkOrderController::class, 'importTemplate'])->name('work-orders.import-template');
    Route::post('work-orders-import', [AssyWorkOrderController::class, 'importExcel'])->name('work-orders.import');
    Route::post('work-orders/{work_order}/repair', [AssyWorkOrderController::class, 'repair'])->name('work-orders.repair');
    Route::post('work-orders/{work_order}/install', [AssyWorkOrderController::class, 'install'])->name('work-orders.install');
    Route::get('api/part-lookup', [AssyWorkOrderController::class, 'partLookup'])->name('api.part-lookup');

    // Ready Stock
    Route::get('ready-stock', [ReadyStockController::class, 'index'])->name('ready-stock.index');

    // Admin Only Routes
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
    });
});
