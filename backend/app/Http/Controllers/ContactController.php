<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ContactService;
use App\Services\ApiResponseService;
use Exception;

class ContactController extends Controller
{
    protected ContactService $contactService;
    protected ApiResponseService $apiResponse;

    public function __construct(ContactService $contactService, ApiResponseService $apiResponse)
    {
        $this->contactService = $contactService;
        $this->apiResponse = $apiResponse;
    }

    /**
     * Display a listing of contacts.
     */
    public function index(): JsonResponse
    {
        try {
            $contacts = $this->contactService->getPaginatedContacts(2);
            return $this->apiResponse->success('Contacts retrieved successfully.', $contacts);
        } catch (Exception $e) {
            return $this->apiResponse->fromException($e, 'Failed to fetch contacts.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
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
