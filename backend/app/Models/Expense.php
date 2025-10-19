<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'custom_fields_list' => 'array',
        'tags' => 'array',
        'created_time' => 'datetime',
        'last_modified_time' => 'datetime',
        'is_billable' => 'boolean',
        'has_attachment' => 'boolean',
    ];

    public function receipts()
    {
        return $this->hasMany(ExpenseReceipt::class, 'expense_id');
    }
}
