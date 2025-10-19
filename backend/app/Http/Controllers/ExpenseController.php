<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ExpenseService;
use App\Services\ApiResponseService;
use Exception;


class ExpenseController extends Controller
{
    protected ExpenseService $expenseService;
    protected ApiResponseService $apiResponse;

    public function __construct(ExpenseService $expenseService, ApiResponseService $apiResponse)
    {
        $this->expenseService = $expenseService;
        $this->apiResponse = $apiResponse;
    }

    /**
     * Display a listing of contacts.
     */
    public function index(): JsonResponse
    {
        try {
            $contacts = $this->expenseService->getPaginatedExpenses(2);
            return $this->apiResponse->success('Expenses retrieved successfully.', $contacts);
        } catch (Exception $e) {
            return $this->apiResponse->fromException($e, 'Failed to fetch expenses.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
