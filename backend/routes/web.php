<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ReceiptController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/receipts/{file}', [ReceiptController::class, 'download'])
    ->where('file', '.*');
