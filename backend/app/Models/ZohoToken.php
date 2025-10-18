<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZohoToken extends Model
{
    protected $fillable = [
        'access_token',
        'refresh_token',
        'api_domain',
        'token_type',
        'expires_in',
        'expires_at',
    ];
}
