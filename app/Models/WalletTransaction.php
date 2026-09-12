<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WalletTransaction extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'type',
        'amount',
        'balance_after',
        'reference',
        'wallet_id',
        'status'
    ];
    // wallet
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
    //  self inject
    public static function Transaction($walletId,$type,$amount,$balance_after,$reference)
    {
        $transaction = self::create([
            'wallet_id'=>$walletId,
            'type'=>$type,
            'amount'=>$amount,
            'balance_after'=>$balance_after,
            'reference'=>$reference
        ]);
        return $transaction;
    }
}
