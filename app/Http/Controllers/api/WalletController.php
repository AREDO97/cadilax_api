<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{

    // create wallet
    public function create(Request $request, Wallet $wallet)
    {
       DB::transaction(function () use($request,$wallet){
        // lock wallet 
        $wallet=Wallet::where('id',$wallet->id)
        ->lockForUpdate()->first();
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
       });
    }
    // withdraw money from wallet
    public function withdraw(Request $request,Wallet $wallet)
    {
       return DB::transaction(function () use($request,$wallet){
            // lock wallet 
        $wallet=Wallet::where('id',$wallet->id)
        ->lockForUpdate()->first();
        $user=$request->user();
        if($user->id !== $wallet->user_id)
            {
             abort(403,'Unauthorised action');   
            }
        // reduce money from wallet by amount to be withdrawn
        // validation
        $request->validate([
            'amount'=>'required'
        ]);
        if($request->amount > $wallet->balance)
            {
                abort(405,'Insufficient wallet balance');
            }
        // decrease wallet balance
        $wallet->decrement('balance',$request->amount);
        $userNewBalance=$wallet->fresh()->balance;
        // record transaction
         /*
        Wallet transactions
        $walletId,$type,$amount,$balance_after,$reference* */
        WalletTransaction::Transaction(
            $wallet->id,
            'cash withdraw',
            $request->amount,
            $userNewBalance,
            $wallet->id . '#'
        );
        return response()->json([
            'message'=>'cash withdraw successiful',
            'new_wallet_balance'=>$userNewBalance
        ]);
       });
    }
    // user wallet
    public function index(Request $request)
    {
        $user=$request->user();
        $userWallet=$user->wallet;
        // response
        return response()->json($userWallet);
    }
}
