<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Stake;
use Illuminate\Http\Request;

class StakeController extends Controller
{
    // create stake
    public function create(Request $request)
    {
             $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin')
        {
        abort(403,'Unauthorised action');
        }
        // validation
        $request->validate([
            'amount'=>'required'
        ]);
        // create
        $stake = Stake::create([
            'amount'=>$request->amount
        ]);
        // response
        return response()->json([
            'message'=>'Stake created successifully',
            'stake'=>$stake
        ]);
    }
    // update stock
    public function update(Request $request,Stake $stake)
    {
             $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin')
        {
              abort(403,'Unauthorised action');
        }
         // validation
        $request->validate([
            'amount'=>'required'
        ]);
        // update stake
        $stake->update([
            'amount'=>$request->amount
        ]);
        // response
        return response()->json([
            'message'=>'Stake updated',
            'new_stake'=>$stake
        ]);
    }
    // delete stake
    public function destroy(Stake $stake)
    {
        $stake->update([
            'status'=>'inactive'
        ]);
        // response
        return response()->json([
            'message'=>'stake removed ',
            'removed_stake'=>$stake
        ]);
    }
    // display stakes 
    public function index()
    {
        $stakes=Stake::where('status','active')->get();
        return response()->json($stakes);
    }
}
