<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZohoAuthController;
use App\Http\Controllers\ZohoController;

Route::get('/zoho/auth/url', [ZohoAuthController::class, 'getAuthUrl']);
Route::get('/zoho/auth/callback', [ZohoAuthController::class, 'handleCallback']);
Route::get('/zoho/status', [ZohoAuthController::class, 'status']);
Route::get('/zoho/connect/url', [ZohoController::class, 'getConnectUrl']);
Route::get('/zoho/connect/callback', [ZohoController::class, 'handleConnectCallback']);
