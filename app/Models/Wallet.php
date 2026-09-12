<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model
{
             use HasFactory;

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
    // transactions
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
