<?php

namespace App\Services;

use App\Models\Expense;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExpenseService
{
    /**
     * Fetch paginated list of expenses.
     */
    public function getPaginatedExpenses(int $perPage = 20): LengthAwarePaginator
    {
        return Expense::latest('created_at')->with('receipts')->paginate($perPage);
    }
}
