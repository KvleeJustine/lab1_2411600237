<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiDashboardController;

/*
|--------------------------------------------------------------------------
| API Routes — Real-Time Inventory Dashboard
|--------------------------------------------------------------------------
| These endpoints are polled by the dashboard JavaScript every 30 seconds
| to keep statistics and low-stock alerts up to date without a page reload.
|
| Auth is via session cookie (web guard) so no Sanctum token is needed for
| the same-origin AJAX calls from our Blade views.
*/

Route::middleware('web')->group(function () {
    Route::get('/dashboard/stats',       [ApiDashboardController::class, 'stats']);
    Route::get('/products/low-stock',    [ApiDashboardController::class, 'lowStock']);
    Route::get('/transactions/recent',   [ApiDashboardController::class, 'recentTransactions']);
});
