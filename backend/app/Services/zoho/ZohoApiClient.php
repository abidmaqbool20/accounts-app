<?php

namespace App\Services\Zoho;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ZohoToken;
use Carbon\Carbon;
use Exception;

class ZohoApiClient
{
    /**
     * Retrieve a valid access token (auto-refresh if expired).
     */
    public function getAccessToken(): string
    {
        $token = ZohoToken::where('token_type_context', 'connect')->first();

        if (!$token) {
            throw new Exception('Zoho not connected', 404);
        }

        // Refresh if expired
        if (!$token->expires_at || $token->expires_at->isPast()) {
            Log::info('Access token expired — refreshing...');
            app(ZohoConnectService::class)->refreshToken();
            $token->refresh();
        }

        return $token->access_token;
    }

    /**
     * Base Zoho API GET request with organization ID automatically appended.
     */
    public function get(string $endpoint, array $params = []): array
    {
        $accessToken = $this->getAccessToken();
        $token = ZohoToken::where('token_type_context', 'connect')->first();

        $url = rtrim($token->api_domain, '/') . '/books/v3/' . ltrim($endpoint, '/');
        $params['organization_id'] = config('services.zoho.organization_id');

        $response = Http::withToken($accessToken)->get($url, $params);
        $json = $response->json();

        if ($response->failed()) {
            Log::error("Zoho GET failed: {$url}", ['response' => $json]);
            throw new Exception('Zoho API request failed', $response->status());
        }

        return $json;
    }

    /**
     * Download a binary file from Zoho API.
     */
    public function download(string $endpoint, array $params = []): array
    {
        $accessToken = $this->getAccessToken();
        $token = ZohoToken::where('token_type_context', 'connect')->first();

        $url = rtrim($token->api_domain, '/') . '/books/v3/' . ltrim($endpoint, '/');
        $params['organization_id'] = config('services.zoho.organization_id');

        // ensure directory exists
        $dir = storage_path('app/zoho/tmp');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $tmpPath = $dir . '/zoho_receipt_' . uniqid() . '.bin';

        // Use sink to write binary directly to file
        Http::withHeaders([
            'Authorization' => "Zoho-oauthtoken {$accessToken}",
        ])
            ->sink($tmpPath)
            ->get($url, $params)
            ->throw();

        // After download, get MIME and extension
        $contentType = mime_content_type($tmpPath) ?: 'application/octet-stream';
        $extension   = match (true) {
            str_contains($contentType, 'png')  => 'png',
            str_contains($contentType, 'jpeg') => 'jpg',
            str_contains($contentType, 'pdf')  => 'pdf',
            default                            => 'bin',
        };

        return [
            'tmp_path'     => $tmpPath,
            'content_type' => $contentType,
            'extension'    => $extension,
        ];
    }
}
