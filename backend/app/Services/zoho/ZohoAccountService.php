<?php

namespace App\Services\Zoho;

use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class ZohoAccountService
{
    protected ZohoApiClient $client;

    public function __construct(ZohoApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch Chart of Accounts from Zoho Books.
     */
    public function fetchChartOfAccounts(): array
    {
        $data = $this->client->get('chartofaccounts');

        if (!isset($data['chartofaccounts'])) {
            Log::error('Failed to fetch Zoho chart of accounts', ['response' => $data]);
            throw new Exception('Failed to fetch chart of accounts', 500);
        }

        // Log::info('Fetched chart of accounts successfully.', ['count' => count($data['chartofaccounts'])]);
        return $data['chartofaccounts'];
    }

    /**
     * Save (or update) Chart of Accounts locally.
     */
    public function saveChartOfAccounts(array $accounts): void
    {
        foreach ($accounts as $accountData) {
            ChartOfAccount::updateOrCreate(
                ['account_id' => $accountData['account_id']],
                [
                    'account_name'         => $accountData['account_name'] ?? null,
                    'account_code'         => $accountData['account_code'] ?? null,
                    'account_type'         => $accountData['account_type'] ?? null,
                    'description'          => $accountData['description'] ?? null,

                    'is_user_created'      => (bool)($accountData['is_user_created'] ?? false),
                    'is_system_account'    => (bool)($accountData['is_system_account'] ?? false),
                    'is_active'            => (bool)($accountData['is_active'] ?? true),
                    'can_show_in_ze'       => (bool)($accountData['can_show_in_ze'] ?? false),

                    'parent_account_id'    => $accountData['parent_account_id'] ?? null,
                    'parent_account_name'  => $accountData['parent_account_name'] ?? null,
                    'depth'                => (int)($accountData['depth'] ?? 0),

                    'has_attachment'       => (bool)($accountData['has_attachment'] ?? false),
                    'is_child_present'     => (bool)($accountData['is_child_present'] ?? false),

                    'child_count'          => isset($accountData['child_count']) && $accountData['child_count'] !== ''
                        ? (int)$accountData['child_count']
                        : null,

                    'documents'            => $accountData['documents'] ?? [],
                    'is_standalone_account' => (bool)($accountData['is_standalone_account'] ?? false),

                    'created_time'         => !empty($accountData['created_time'])
                        ? Carbon::parse($accountData['created_time'])
                        : null,
                    'last_modified_time'   => !empty($accountData['last_modified_time'])
                        ? Carbon::parse($accountData['last_modified_time'])
                        : null,
                ]
            );
        }

        // Log::info('Chart of accounts saved successfully.', ['count' => count($accounts)]);
    }

    /**
     * Sync Chart of Accounts end-to-end.
     */
    public function sync(): array
    {
        $accounts = $this->fetchChartOfAccounts();
        $this->saveChartOfAccounts($accounts);

        return [
            'message' => 'Chart of accounts synced successfully.',
            'count'   => count($accounts),
        ];
    }
}
