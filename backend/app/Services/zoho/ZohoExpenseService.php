<?php

namespace App\Services\Zoho;

use App\Models\Expense;
use App\Models\ExpenseReceipt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;

class ZohoExpenseService
{
    protected ZohoApiClient $client;

    public function __construct(ZohoApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch all expenses from Zoho Books.
     */
    public function fetchExpenses(): array
    {
        $data = $this->client->get('expenses');

        if (!isset($data['expenses'])) {
            Log::error('Failed to fetch Zoho expenses', ['response' => $data]);
            throw new Exception('Failed to fetch expenses', 500);
        }

        // Log::info('Fetched Zoho expenses successfully.', ['count' => count($data['expenses'])]);
        return $data['expenses'];
    }

    /**
     * Save or update expenses locally.
     */
    public function saveExpenses(array $expenses): void
    {
        foreach ($expenses as $expenseData) {
            $mapped = [
                'expense_id'                => $expenseData['expense_id'] ?? null,
                'date'                      => $expenseData['date'] ?? null,
                'user_name'                 => $expenseData['user_name'] ?? null,
                'paid_through_account_name' => $expenseData['paid_through_account_name'] ?? null,
                'account_name'              => $expenseData['account_name'] ?? null,
                'description'               => $expenseData['description'] ?? null,
                'currency_id'               => $expenseData['currency_id'] ?? null,
                'currency_code'             => $expenseData['currency_code'] ?? null,
                'bcy_total'                 => $expenseData['bcy_total'] ?? 0,
                'bcy_total_without_tax'     => $expenseData['bcy_total_without_tax'] ?? 0,
                'total'                     => $expenseData['total'] ?? 0,
                'total_without_tax'         => $expenseData['total_without_tax'] ?? 0,
                'is_billable'               => $expenseData['is_billable'] ?? false,
                'reference_number'          => $expenseData['reference_number'] ?? null,
                'customer_id'               => $expenseData['customer_id'] ?? null,
                'customer_name'             => $expenseData['customer_name'] ?? null,
                'vendor_id'                 => $expenseData['vendor_id'] ?? null,
                'vendor_name'               => $expenseData['vendor_name'] ?? null,
                'status'                    => $expenseData['status'] ?? null,
                'created_time'              => $expenseData['created_time'] ?? null,
                'last_modified_time'        => $expenseData['last_modified_time'] ?? null,
                'expense_receipt_name'      => $expenseData['expense_receipt_name'] ?? null,
                'exchange_rate'             => $expenseData['exchange_rate'] ?? 1,
                'distance'                  => $expenseData['distance'] ?? 0,
                'mileage_rate'              => $expenseData['mileage_rate'] ?? 0,
                'mileage_unit'              => $expenseData['mileage_unit'] ?? 'km',
                'mileage_type'              => $expenseData['mileage_type'] ?? null,
                'expense_type'              => $expenseData['expense_type'] ?? null,
                'report_id'                 => $expenseData['report_id'] ?? null,
                'report_name'               => $expenseData['report_name'] ?? null,
                'report_number'             => $expenseData['report_number'] ?? null,
                'has_attachment'            => !empty($expenseData['has_attachment']),
                'custom_fields_list'        => !empty($expenseData['custom_fields_list'])
                    ? json_encode($expenseData['custom_fields_list'])
                    : null,
                'tags'                      => !empty($expenseData['tags'])
                    ? json_encode($expenseData['tags'])
                    : null,
            ];

            $expense = Expense::updateOrCreate(
                ['expense_id' => $mapped['expense_id']],
                $mapped
            );

            if (!empty($mapped['has_attachment'])) {
                try {
                    $file = $this->fetchExpenseReceipt($mapped['expense_id']);

                    ExpenseReceipt::updateOrCreate(
                        ['expense_id' => $expense->id],
                        [
                            'expense_id' => $expense->id,
                            'file_name' => $file['file_name'],
                            'mime_type' => $file['mime_type'],
                            'file_download_url' => $file['file_download_url'],
                            'file_size' => $file['file_size'] ?? null,
                            'uploaded_time' => $file['uploaded_time'] ?? null,
                            'local_path' => $file['local_path'] ?? null,
                        ]
                    );

                    // Log::info("Expense receipt stored successfully", ['expense_id' => $expense->id]);
                } catch (\Throwable $e) {
                    Log::error("Failed to save expense receipt", [
                        'expense_id' => $mapped['expense_id'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // Log::info('Expenses saved successfully.', ['count' => count($expenses)]);
    }

    /**
     * Fetch and save an expense receipt.
     */
    public function fetchExpenseReceipt(string $expenseId): array
    {
        try {
            $result = $this->client->download("expenses/{$expenseId}/receipt");

            $tmpPath = $result['tmp_path'];
            $contentType = $result['content_type'];
            $extension = $result['extension'];

            $fileName = "expense_{$expenseId}.{$extension}";
            $filePath = "zoho/receipts/{$fileName}";

            // Ensure receipts directory exists
            Storage::makeDirectory('zoho/receipts');

            // Move the temporary file into the receipts folder
            Storage::put("{$filePath}", file_get_contents($tmpPath), 'public');

            // Delete the temp file
            @unlink($tmpPath);

            $fileUrl = url("/receipts/{$fileName}");
            $fileSize = Storage::size($filePath);

            return [
                'file_name' => $fileName,
                'mime_type' => $contentType,
                'file_size' => $fileSize,
                'file_download_url' => $fileUrl,
                'local_path' => storage_path("app/{$filePath}"),
                'uploaded_time' => now()->toDateTimeString(),
            ];

            // Log::info("Receipt saved successfully", $meta);
            return $meta;
        } catch (\Throwable $e) {
            Log::error("Error fetching expense receipt", [
                'expense_id' => $expenseId,
                'error' => $e->getMessage(),
            ]);

            throw new Exception("Failed to fetch receipt: " . $e->getMessage());
        }
    }


    /**
     * Full sync: Fetch + Save all expenses.
     */
    public function sync(): array
    {
        $expenses = $this->fetchExpenses();
        $this->saveExpenses($expenses);

        return [
            'message' => 'Expenses synced successfully.',
            'count'   => count($expenses),
        ];
    }
}
