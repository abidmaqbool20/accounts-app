<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ApiResponseService
{
    /**
     * Standard success response.
     */
    public function success(string $message = 'Success', mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Standard error response (logs automatically).
     */
    public function error(string $message, ?string $details = null, int $status = 500): JsonResponse
    {
        Log::error($message, ['details' => $details]);
        return response()->json([
            'success' => false,
            'error'   => $message,
            'details' => $details,
        ], $status);
    }

    /**
     * Safely normalize any thrown exception.
     */
    public function fromException(\Throwable $e, string $context = 'Unexpected error'): JsonResponse
    {
        $status = (int) $e->getCode();
        if ($status < 100 || $status > 599) {
            $status = 500;
        }

        Log::error($context, ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

        return $this->error($context, $e->getMessage(), $status);
    }
}
