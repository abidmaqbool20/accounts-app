<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class VerifyZohoToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Missing Zoho Bearer token'], 401);
        }

        $accessToken = trim(str_replace('Bearer', '', $authHeader));
        $cacheKey = "zoho_token_valid:" . sha1($accessToken);

        if (Cache::has($cacheKey)) {
            $cachedData = Cache::get($cacheKey);
            $request->attributes->set('zoho_orgs', $cachedData['organizations'] ?? []);
            return $next($request);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
        ])->get('https://www.zohoapis.com/books/v3/organizations');

        if ($response->failed()) {
            return response()->json([
                'message' => 'Invalid or expired Zoho token',
                'error' => $response->json(),
            ], 401);
        }

        $data = $response->json();
        $organizations = $data['organizations'] ?? [];

        Cache::put($cacheKey, ['organizations' => $organizations], now()->addMinutes(30));


        $request->attributes->set('zoho_orgs', $organizations);

        return $next($request);
    }
}
