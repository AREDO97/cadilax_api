<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class WallettransactionsController extends Controller
{
    // wallet transactions
    public function index(Request $request)
    {
        $user=$request->user();
        $transactions=$user->wallet->transactions()
        ->where('status','active')->latest()->paginate(10);
        // response
        return response()->json([
            'user'=>$user,
            'wallet_transactions'=>$transactions
        ]);
    }
    // delete transaction history
    public function destroy(Request $request,WalletTransaction $transaction)
    {
        $user=$request->user();
        if($user->id !== $transaction->wallet->user_id)
            {
                abort(403,'Unauthorised action !!');
            }
        $transaction->update([
            'status'=>'deleted'
        ]);
        // response
        return response()->json([
            'message'=>'Transaction history deleted',
            'transaction'=>$transaction
        ]);
    }
}
