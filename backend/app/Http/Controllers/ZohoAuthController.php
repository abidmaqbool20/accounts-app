<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ZohoToken;
use Carbon\Carbon;

class ZohoAuthController extends Controller
{
    public function getAuthUrl()
    {
        $url = 'https://accounts.zoho.com/oauth/v2/auth?' . http_build_query([
            'scope' => 'ZohoBooks.fullaccess.all',
            'client_id' => config('services.zoho.client_id'),
            'response_type' => 'code',
            'access_type' => 'offline',
            'redirect_uri' => config('services.zoho.redirect_uri'),
        ]);

        return response()->json(['auth_url' => $url]);
    }

    public function handleCallback(Request $request)
    {
        $code = $request->get('code');

        $response = Http::asForm()->post('https://accounts.zoho.com/oauth/v2/token', [
            'code' => $code,
            'client_id' => config('services.zoho.client_id'),
            'client_secret' => config('services.zoho.client_secret'),
            'redirect_uri' => config('services.zoho.redirect_uri'),
            'grant_type' => 'authorization_code',
        ]);

        $data = $response->json();

        if (!isset($data['access_token'])) {
            return response()->json([
                'error' => 'Token exchange failed',
                'details' => $data,
            ], 400);
        }

        // Calculate expiry
        $expiresAt = Carbon::now()->addSeconds($data['expires_in']);

        // Save or update record
        ZohoToken::updateOrCreate([], [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? null,
            'api_domain' => $data['api_domain'] ?? null,
            'token_type' => $data['token_type'] ?? 'Bearer',
            'expires_in' => $data['expires_in'] ?? null,
            'expires_at' => $expiresAt,
        ]);

        // Redirect back to frontend (you can show "connected" state there)
        return redirect(env('FRONTEND_URL') . '/zoho/callback?connected=true&&token=' . $data['access_token']);
    }

    // Check if connected (frontend can call this)
    public function status()
    {
        $token = ZohoToken::first();
        return response()->json([
            'connected' => (bool) $token,
            'expires_at' => $token?->expires_at,
        ]);
    }
}
