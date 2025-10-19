<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ChartOfAccountService;
use App\Services\ApiResponseService;
use Exception;

class ChartOfAccountController extends Controller
{
    protected ChartOfAccountService $chartOfAccountService;
    protected ApiResponseService $apiResponse;

    public function __construct(ChartOfAccountService $chartOfAccountService, ApiResponseService $apiResponse)
    {
        $this->chartOfAccountService = $chartOfAccountService;
        $this->apiResponse = $apiResponse;
    }

    /**
     * Display a listing of chart of accounts.
     */
    public function index(): JsonResponse
    {
        try {
            $chartOfAccounts = $this->chartOfAccountService->getPaginatedChartOfAccounts(20);
            return $this->apiResponse->success('Chart of accounts retrieved successfully.', $chartOfAccounts);
        } catch (Exception $e) {
            return $this->apiResponse->fromException($e, 'Failed to fetch chart of accounts.');
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
