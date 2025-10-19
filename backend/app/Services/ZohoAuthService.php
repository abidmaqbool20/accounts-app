<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\ZohoToken;
use Carbon\Carbon;
use Exception;

class ZohoAuthService
{
    /**
     * Generate Zoho OAuth authorization URL.
     */
    public function getAuthUrl(): string
    {
        $params = [
            'scope' => 'ZohoBooks.fullaccess.all',
            'client_id' => config('services.zoho.client_id'),
            'response_type' => 'code',
            'access_type' => 'offline',
            'redirect_uri' => config('services.zoho.redirect_uri'),
        ];

        return 'https://accounts.zoho.com/oauth/v2/auth?' . http_build_query($params);
    }

    /**
     * Handle OAuth callback, exchange code for token, and persist.
     */
    public function handleCallback(string $code): array
    {
        $response = Http::asForm()->post('https://accounts.zoho.com/oauth/v2/token', [
            'code' => $code,
            'client_id' => config('services.zoho.client_id'),
            'client_secret' => config('services.zoho.client_secret'),
            'redirect_uri' => config('services.zoho.redirect_uri'),
            'grant_type' => 'authorization_code',
        ]);

        $data = $response->json();

        if (!isset($data['access_token'])) {
            throw new Exception('Token exchange failed', 400);
        }

        $expiresAt = Carbon::now()->addSeconds($data['expires_in']);

        ZohoToken::updateOrCreate([], [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? null,
            'api_domain'   => $data['api_domain'] ?? null,
            'token_type'   => $data['token_type'] ?? 'Bearer',
            'expires_in'   => $data['expires_in'] ?? null,
            'expires_at'   => $expiresAt,
        ]);

        return $data;
    }

    /**
     * Check connection status and expiry.
     */
    public function getStatus(): array
    {
        $token = ZohoToken::first();

        return [
            'connected'  => (bool) $token,
            'expires_at' => $token?->expires_at,
        ];
    }
}
