<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class ReceiptController extends Controller
{
    public function download($file)
    {
        $path = "zoho/receipts/{$file}";
        if (!Storage::disk()->exists($path)) {
            abort(404);
        }

        $mime = Storage::disk()->mimeType($path) ?? 'application/octet-stream';
        $name = basename($path);

        return response()->streamDownload(function () use ($path) {
            echo Storage::disk()->get($path);
        }, $name, ['Content-Type' => $mime]);
    }
}
