<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    use HasFactory;


    protected $guarded = [];

    protected $casts = [
        'is_user_created'       => 'boolean',
        'is_system_account'     => 'boolean',
        'is_active'             => 'boolean',
        'can_show_in_ze'        => 'boolean',
        'has_attachment'        => 'boolean',
        'is_child_present'      => 'boolean',
        'is_standalone_account' => 'boolean',
        'documents'             => 'array',
        'created_time'          => 'datetime',
        'last_modified_time'    => 'datetime',
    ];
}
