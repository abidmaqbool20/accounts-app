<?php

namespace App\Services\Zoho;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ZohoToken;
use Carbon\Carbon;
use Exception;

class ZohoConnectService
{
    /**
     * Generate Zoho Books connect URL.
     */
    public function generateConnectUrl(): string
    {
        $params = [
            'scope'         => 'ZohoBooks.fullaccess.all',
            'client_id'     => config('services.zoho.client_id'),
            'response_type' => 'code',
            'access_type'   => 'offline',
            'redirect_uri'  => config('services.zoho.connect_redirect_uri'),
            'prompt'        => 'consent',
        ];

        return config('services.zoho.connect_url') . 'auth?' . http_build_query($params);
    }

    /**
     * Handle Zoho callback and save tokens.
     */
    public function handleConnectCallback(string $code): array
    {
        $response = Http::asForm()->post(config('services.zoho.connect_url') . 'token', [
            'code'          => $code,
            'client_id'     => config('services.zoho.client_id'),
            'client_secret' => config('services.zoho.client_secret'),
            'redirect_uri'  => config('services.zoho.connect_redirect_uri'),
            'grant_type'    => 'authorization_code',
        ]);

        $data = $response->json();

        if (!isset($data['access_token'])) {
            Log::error('Zoho connect token exchange failed', ['response' => $data]);
            throw new Exception('Token exchange failed', 400);
        }

        $expiresAt = Carbon::now()->addSeconds($data['expires_in']);
        $existing = ZohoToken::where('token_type_context', 'connect')->first();
        $refreshToken = $data['refresh_token'] ?? $existing?->refresh_token;

        ZohoToken::updateOrCreate(
            ['token_type_context' => 'connect'],
            [
                'access_token'  => $data['access_token'],
                'refresh_token' => $refreshToken,
                'api_domain'    => $data['api_domain'] ?? 'https://www.zohoapis.com',
                'token_type'    => $data['token_type'] ?? 'Bearer',
                'expires_in'    => $data['expires_in'] ?? null,
                'expires_at'    => $expiresAt,
            ]
        );

        Log::info('Zoho Connect token saved successfully.');
        return $data;
    }

    /**
     * Refresh Zoho access token when expired.
     */
    public function refreshToken(): array
    {
        $token = ZohoToken::where('token_type_context', 'connect')->first();

        if (!$token || !$token->refresh_token) {
            throw new Exception('No refresh token stored', 404);
        }

        if ($token->expires_at && $token->expires_at->isFuture()) {
            return [
                'message'      => 'Access token still valid',
                'access_token' => $token->access_token,
                'expires_at'   => $token->expires_at,
            ];
        }

        $response = Http::asForm()->post(config('services.zoho.connect_url') . 'token', [
            'refresh_token' => $token->refresh_token,
            'client_id'     => config('services.zoho.client_id'),
            'client_secret' => config('services.zoho.client_secret'),
            'grant_type'    => 'refresh_token',
        ]);

        $data = $response->json();

        if (!isset($data['access_token'])) {
            Log::error('Zoho token refresh failed', ['response' => $data]);
            throw new Exception('Token refresh failed', 500);
        }

        $token->update([
            'access_token' => $data['access_token'],
            'expires_in'   => $data['expires_in'] ?? 3600,
            'expires_at'   => now()->addSeconds($data['expires_in'] ?? 3600),
        ]);

        Log::info('Zoho token refreshed successfully.');

        return [
            'message'      => 'Access token refreshed successfully',
            'access_token' => $data['access_token'],
            'expires_at'   => $token->expires_at,
        ];
    }

    /**
     * Get current connection status.
     */
    public function getStatus(): array
    {
        $token = ZohoToken::where('token_type_context', 'connect')->first();

        return [
            'connected'  => (bool) $token,
            'expires_at' => $token?->expires_at,
            'api_domain' => $token?->api_domain,
        ];
    }
}
