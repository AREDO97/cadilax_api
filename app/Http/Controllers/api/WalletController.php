<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\GosentePayService;
use App\Models\Deposit;

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
        // wallet amount > 3k
         if($request->amount < 3000)
            {
                abort(405,'You can withdraw at least 3000');
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

   

    public function initiatePayment(Request $request, GosentePayService $gosente)
    {
        $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric|min:500',
            'email' => 'required|email',
        ]);

        $reference = 'GPS-ORD-' . uniqid();

        // 1. Save record as pending in database first
        $deposit = Deposit::create([
            'reference' => $reference,
            'phone' => $request->phone,
            'amount' => $request->amount,
            'email' => $request->email,
            'status' => 'pending',
        ]);

        // 2. Trigger the gateway request
        $response = $gosente->deposit([
            'phone'    => $request->phone,
            'amount'   => $request->amount,
            'email'    => $request->email,
            'ref'      => $reference,
            'callback' => route('api.gosente.webhook'),
        ]);

        // Optional: Save initial gateway response payload
        $deposit->update(['gateway_response' => $response]);

        return response()->json([
            'reference' => $reference,
            'gateway_response' => $response,
        ]);
    }

    public function handleWebhook(Request $request)
    {
        $data = $request->all();

        $reference = $data['ref'] ?? null;
        $status = $data['status'] ?? null; // e.g., 'successful' or 'failed'

        if ($reference) {
            // Find deposit by reference and update status automatically
            $deposit = Deposit::where('reference', $reference)->first();

            if ($deposit) {
                $deposit->update([
                    'status' => $status === 'successful' ? 'successful' : 'failed',
                    'gateway_response' => $data,
                ]);
            }
        }

        return response()->json(['status' => 'received'], 200);
    }

    public function initiateWithdrawal(Request $request, GosentePayService $gosente)
    {
        $reference = 'GPS-PO-' . uniqid();

        $response = $gosente->withdraw([
            'phone'  => $request->phone,
            'amount' => $request->amount,
            'email'  => $request->email,
            'reason' => 'Sandbox test payout',
            'ref'    => $reference,
        ]);

        return response()->json([
            'reference' => $reference,
            'gateway_response' => $response,
        ]);
    }

}
