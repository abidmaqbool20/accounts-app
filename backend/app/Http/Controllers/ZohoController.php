<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ZohoToken;
use Carbon\Carbon;

class ZohoController extends Controller
{
    /**
     * Step 1: Generate the Zoho Books authorization URL.
     */
    public function getConnectUrl()
    {
        $clientId     = config('services.zoho.client_id');
        $redirectUri  = config('services.zoho.connect_redirect_uri');
        $scope        = 'ZohoBooks.fullaccess.all';
        $responseType = 'code';
        $accessType   = 'offline';   // ensures refresh_token is permanent
        $prompt       = 'consent';   // forces consent screen to appear once

        $authUrl = "https://accounts.zoho.com/oauth/v2/auth?" . http_build_query([
            'scope'         => $scope,
            'client_id'     => $clientId,
            'response_type' => $responseType,
            'access_type'   => $accessType,
            'redirect_uri'  => $redirectUri,
            'prompt'        => $prompt,
        ]);

        return response()->json(['auth_url' => $authUrl]);
    }

    /**
     * Step 2: Handle callback after user grants permission.
     */
    public function handleConnectCallback(Request $request)
    {
        $code = $request->get('code');

        if (!$code) {
            return response()->json(['error' => 'Authorization code missing'], 400);
        }

        try {
            $clientId     = config('services.zoho.client_id');
            $clientSecret = config('services.zoho.client_secret');
            $redirectUri  = config('services.zoho.connect_redirect_uri');

            // Exchange authorization code for tokens
            $response = Http::asForm()->post('https://accounts.zoho.com/oauth/v2/token', [
                'code'          => $code,
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri'  => $redirectUri,
                'grant_type'    => 'authorization_code',
            ]);

            $data = $response->json();

            if (!isset($data['access_token'])) {
                Log::error('Zoho connect token exchange failed', ['response' => $data]);
                return response()->json(['error' => 'Token exchange failed', 'details' => $data], 400);
            }

            $expiresAt = Carbon::now()->addSeconds($data['expires_in']);

            // If Zoho didn’t return a new refresh token, keep the old one
            $existing = ZohoToken::where('token_type_context', 'connect')->first();
            $refreshToken = $data['refresh_token'] ?? $existing?->refresh_token;

            // Store or update the application-level connection token
            ZohoToken::updateOrCreate(
                ['token_type_context' => 'connect'],
                [
                    'access_token'  => $data['access_token'],
                    'refresh_token' => $refreshToken,
                    'api_domain'    => $data['api_domain'] ?? null,
                    'token_type'    => $data['token_type'] ?? 'Bearer',
                    'expires_in'    => $data['expires_in'] ?? null,
                    'expires_at'    => $expiresAt,
                ]
            );

            Log::info('Zoho Connect token saved successfully.');

            // Redirect back to your frontend
            return redirect(env('FRONTEND_URL') . '/dashboard?zoho=connected&&token=' . $data['access_token']);
        } catch (\Throwable $e) {
            Log::error('Zoho connect callback error', ['message' => $e->getMessage()]);
            return response()->json([
                'error'   => 'Callback processing failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Step 3: Refresh the access token automatically when it expires.
     */
    public function refreshToken()
    {
        $token = ZohoToken::where('token_type_context', 'connect')->first();

        if (!$token || !$token->refresh_token) {
            return response()->json(['error' => 'No refresh token stored'], 404);
        }

        // Only refresh if expired (safety)
        if ($token->expires_at && $token->expires_at->isFuture()) {
            return response()->json([
                'message' => 'Access token still valid',
                'access_token' => $token->access_token,
                'expires_at' => $token->expires_at,
            ]);
        }

        try {
            $clientId     = config('services.zoho.client_id');
            $clientSecret = config('services.zoho.client_secret');

            $response = Http::asForm()->post('https://accounts.zoho.com/oauth/v2/token', [
                'refresh_token' => $token->refresh_token,
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'grant_type'    => 'refresh_token',
            ]);

            $data = $response->json();

            if (!isset($data['access_token'])) {
                Log::error('Zoho token refresh failed', ['response' => $data]);
                return response()->json(['error' => 'Refresh failed', 'details' => $data], 500);
            }

            $token->update([
                'access_token' => $data['access_token'],
                'expires_in'   => $data['expires_in'] ?? 3600,
                'expires_at'   => now()->addSeconds($data['expires_in'] ?? 3600),
            ]);

            return response()->json([
                'message' => 'Access token refreshed successfully',
                'access_token' => $data['access_token'],
                'expires_at' => $token->expires_at,
            ]);
        } catch (\Throwable $e) {
            Log::error('Zoho token refresh exception', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Token refresh failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Step 4: Check connection status.
     */
    public function status()
    {
        $token = ZohoToken::where('token_type_context', 'connect')->first();

        return response()->json([
            'connected'  => (bool) $token,
            'expires_at' => $token?->expires_at,
            'api_domain' => $token?->api_domain,
        ]);
    }
}
