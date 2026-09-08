<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    // create wallet
    public function create(Request $request)
    {
        $user=$request->user();
        // validation
        $request->validate([
            'balance'=>'required'
        ]);
        // create wallet
        $wallet = Wallet::create([
            'user_id'=>$user->id,
            'balance'=>$request->balance ?? 10000
        ]);
        // return response
        return response()->json([
            'message'=>'Wallet created successiful',
            'wallet'=>$wallet
        ]);
    }
}
