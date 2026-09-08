<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class WalletController extends Controller
{

    // create wallet
    public function create(Request $request, Wallet $wallet)
    {
        $user=$request->user();
        // check real owner
        if($user->id !== $wallet->user->id)
            {
                abort(403,'Unauthorised action !!');
            }
        // validation
        $request->validate([
            'balance'=>'required'
        ]);
        // user current balance
        $userCurrentBalance=$user->wallet->balance;
        // user new balance
        $userNewBalance=$userCurrentBalance + $request->balance;
        // create wallet
        $wallet->update([
            'balance'=>$userNewBalance
        ]);
        
        // transaction history
        $transactionHistory = WalletTransaction::Transaction(
            $user->wallet->id,
            'cash deposit',
            $request->balance,
            $userNewBalance,
            $wallet->id.'#'
        );
        // return response
        return response()->json([
            'message'=>'You sucessiful deposited money to your Wallet',
            'wallet'=>$wallet,
            'transaction_hostory'=>$transactionHistory
        ]);
    }
}
