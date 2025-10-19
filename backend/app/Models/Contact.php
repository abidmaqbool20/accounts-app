<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;


    protected $guarded = [];

    protected $casts = [
        'is_linked_with_zohocrm'              => 'boolean',
        'ach_supported'                       => 'boolean',
        'has_attachment'                      => 'boolean',
        'custom_fields'                       => 'array',
        'custom_field_hash'                   => 'array',
        'tags'                                => 'array',
        'created_time'                        => 'datetime',
        'last_modified_time'                  => 'datetime',
        'outstanding_receivable_amount'       => 'decimal:2',
        'outstanding_receivable_amount_bcy'   => 'decimal:2',
        'outstanding_payable_amount'          => 'decimal:2',
        'outstanding_payable_amount_bcy'      => 'decimal:2',
        'unused_credits_receivable_amount'    => 'decimal:2',
        'unused_credits_receivable_amount_bcy' => 'decimal:2',
        'unused_credits_payable_amount'       => 'decimal:2',
        'unused_credits_payable_amount_bcy'   => 'decimal:2',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
