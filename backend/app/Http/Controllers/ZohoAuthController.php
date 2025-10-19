<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ZohoAuthService;
use App\Services\ApiResponseService;
use Exception;

class ZohoAuthController extends Controller
{
    protected ZohoAuthService $zohoAuthService;
    protected ApiResponseService $apiResponse;

    public function __construct(ZohoAuthService $zohoAuthService, ApiResponseService $apiResponse)
    {
        $this->zohoAuthService = $zohoAuthService;
        $this->apiResponse = $apiResponse;
    }

    public function getAuthUrl()
    {
        try {
            $authUrl = $this->zohoAuthService->getAuthUrl();
            return $this->apiResponse->success('Authorization URL generated successfully.', [
                'auth_url' => $authUrl,
            ]);
        } catch (Exception $e) {
            return $this->apiResponse->fromException($e, 'Failed to generate authorization URL.');
        }
    }

    public function handleCallback(Request $request)
    {
        try {
            $code = $request->get('code');

            if (!$code) {
                return $this->apiResponse->error('Authorization code missing.', null, 400);
            }

            $data = $this->zohoAuthService->handleCallback($code);

            // redirect to frontend after successful auth
            return redirect(
                env('FRONTEND_URL') . '/zoho/callback?connected=true&&token=' . $data['access_token']
            );
        } catch (Exception $e) {
            return $this->apiResponse->fromException($e, 'Zoho authorization failed.');
        }
    }

    public function status()
    {
        try {
            $status = $this->zohoAuthService->getStatus();
            return $this->apiResponse->success('Zoho connection status retrieved.', $status);
        } catch (Exception $e) {
            return $this->apiResponse->fromException($e, 'Failed to get Zoho connection status.');
        }
    }
}
