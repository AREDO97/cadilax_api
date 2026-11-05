<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $fillable = [
        'reference',
        'phone',
        'amount',
        'email',
        'status',
        'gateway_response',
    ];

    protected $casts = [
        'gateway_response' => 'array',
    ];
}