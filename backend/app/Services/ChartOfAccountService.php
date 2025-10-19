<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ChartOfAccountService
{
    /**
     * Fetch paginated list of contacts.
     */
    public function getPaginatedChartOfAccounts(int $perPage = 20): LengthAwarePaginator
    {
        return ChartOfAccount::latest('created_time')->paginate($perPage);
    }
}
