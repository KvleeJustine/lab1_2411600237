<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\ReportController;

// Root redirect to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes (provided by Breeze)
require __DIR__.'/auth.php';

// Protected routes — require authentication
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Mark a single notification as read
    Route::post('/notifications/{id}/read', function (string $id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return back();
    })->name('notifications.read');

    // Mark all notifications as read
    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    })->name('notifications.readAll');

    // Products CRUD
    Route::resource('products', ProductController::class);

    // Inventory Transactions (no edit/update — transactions are immutable)
    Route::resource('transactions', InventoryTransactionController::class)
        ->except(['edit', 'update']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});
