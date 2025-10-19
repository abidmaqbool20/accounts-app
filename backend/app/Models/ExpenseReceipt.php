<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'attachment_id',
        'file_name',
        'file_size',
        'file_download_url',
        'uploaded_time',
        'mime_type',
        'downloaded',
    ];

    protected $casts = [
        'uploaded_time' => 'datetime',
        'downloaded' => 'boolean',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id');
    }
}
