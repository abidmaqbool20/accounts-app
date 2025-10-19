<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZohoAuthController;
use App\Http\Controllers\ZohoController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\ExpenseController;

// Public routes (used for Zoho OAuth)
Route::get('/zoho/auth/url', [ZohoAuthController::class, 'getAuthUrl']);
Route::get('/zoho/auth/callback', [ZohoAuthController::class, 'handleCallback']);
Route::get('/zoho/connect/callback', [ZohoController::class, 'handleConnectCallback']);

// ✅ Protected routes
Route::middleware('zoho.token')->group(function () {

    Route::get('/zoho/connect/url', [ZohoController::class, 'getConnectUrl']);

    Route::middleware('zoho.connected')->group(function () {
        Route::get('/zoho/chart-of-accounts', [ZohoController::class, 'syncChartOfAccounts']);
        Route::get('/zoho/contacts', [ZohoController::class, 'syncContacts']);
        Route::get('/zoho/expenses', [ZohoController::class, 'syncExpenses']);
    });

    Route::apiResource('contacts', ContactController::class);
    Route::apiResource('chart-of-accounts', ChartOfAccountController::class);
    Route::apiResource('expenses', ExpenseController::class);
});
