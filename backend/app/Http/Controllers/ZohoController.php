<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiResponseService;
use App\Services\Zoho\ZohoConnectService;
use App\Services\Zoho\ZohoAccountService;
use App\Services\Zoho\ZohoContactService;
use App\Services\Zoho\ZohoExpenseService;
use Exception;

class ZohoController extends Controller
{
    protected ZohoConnectService $authService;
    protected ZohoAccountService $accountService;
    protected ZohoContactService $contactService;
    protected ZohoExpenseService $expenseService;
    protected ApiResponseService $apiResponse;

    public function __construct(
        ZohoConnectService $authService,
        ZohoAccountService $accountService,
        ZohoContactService $contactService,
        ZohoExpenseService $expenseService,
        ApiResponseService $apiResponse
    ) {
        $this->authService = $authService;
        $this->accountService = $accountService;
        $this->contactService = $contactService;
        $this->expenseService = $expenseService;
        $this->apiResponse = $apiResponse;
    }

    /**
     * Generate Zoho Books connection URL.
     */
    public function getConnectUrl()
    {
        $authUrl = $this->authService->generateConnectUrl();
        return $this->apiResponse->success('Auth URL generated successfully.', ['auth_url' => $authUrl]);
    }

    /**
     * Handle OAuth callback.
     */
    public function handleConnectCallback(Request $request)
    {
        $code = $request->get('code');
        if (!$code) {
            return $this->apiResponse->error('Authorization code missing.', null, 400);
        }

        try {
            $data = $this->authService->handleConnectCallback($code);
            $redirectUrl = rtrim(env('FRONTEND_URL'), '/') . '/dashboard?zoho=connected&&token=' . $data['access_token'];
            return redirect($redirectUrl);
        } catch (Exception $e) {
            return $this->apiResponse->fromException($e, 'Zoho callback failed');
        }
    }

    /**
     * Sync Chart of Accounts (fetch + save).
     */
    public function syncChartOfAccounts()
    {
        try {
            $result = $this->accountService->sync();
            return $this->apiResponse->success($result['message'], ['count' => $result['count']]);
        } catch (Exception $e) {
            return $this->apiResponse->fromException($e, 'Failed to sync chart of accounts');
        }
    }

    /**
     * Sync Contacts (fetch + save).
     */
    public function syncContacts()
    {
        try {
            $result = $this->contactService->sync();
            return $this->apiResponse->success($result['message'], ['count' => $result['count']]);
        } catch (Exception $e) {
            return $this->apiResponse->fromException($e, 'Failed to sync contacts');
        }
    }

    /**
     * Sync Expenses (fetch + save + receipts).
     */
    public function syncExpenses()
    {
        try {
            $result = $this->expenseService->sync();
            return $this->apiResponse->success($result['message'], ['count' => $result['count']]);
        } catch (Exception $e) {
            return $this->apiResponse->fromException($e, 'Failed to sync expenses');
        }
    }

    /**
     * Get Zoho connection status.
     */
    public function getConnectionStatus()
    {
        try {
            $status = $this->authService->getStatus();
            return $this->apiResponse->success('Connection status retrieved successfully.', $status);
        } catch (Exception $e) {
            return $this->apiResponse->fromException($e, 'Failed to get connection status');
        }
    }
}
