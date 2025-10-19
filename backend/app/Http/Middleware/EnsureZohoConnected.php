<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ZohoToken;
use Exception;
use Illuminate\Support\Facades\Log;

class EnsureZohoConnected
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = ZohoToken::latest()->first();
            if (!$token || !$token->access_token) {
                return response()->json(['message' => 'No Zoho access token found. Please connect your Zoho account.'], 401);
            }

            $accessToken = $token->access_token;
            $cacheKey = "zoho_token_valid:" . sha1($accessToken);

            if (Cache::has($cacheKey)) {
                $cachedData = Cache::get($cacheKey);
                $request->attributes->set('zoho_orgs', $cachedData['organizations'] ?? []);
                return $next($request);
            }

            $response = Http::withToken($token->access_token)
                ->get($token->api_domain . '/books/v3/organizations', [
                    'organization_id' => config('services.zoho.organization_id'),
                ]);


            if ($response->failed()) {
                Log::error('Zoho token verification failed: ', $response->json());
                return response()->json([
                    'message' => 'Invalid or expired Zoho token',
                    'error'   => $response->json(),
                ], 401);
            }

            $data = $response->json();
            $organizations = $data['organizations'] ?? [];

            Cache::put($cacheKey, ['organizations' => $organizations], now()->addMinutes(30));

            $request->attributes->set('zoho_orgs', $organizations);

            return $next($request);
        } catch (Exception $e) {
            Log::error('Zoho Middleware Error: ' . $e->getMessage());
            return response()->json(['message' => 'Zoho verification failed.'], 500);
        }
    }
}
