<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    // allowed
    protected $fillable = [
        'user_id',
        'balance'
    ];
    // user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
