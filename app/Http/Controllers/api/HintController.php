<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Hint;
use Illuminate\Http\Request;

class HintController extends Controller
{
    /*create hint, only admins can create hints */
    public function create(Request $request)
    {
        $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin'){
        abort(403,'Unauthorised action');
    }
    // valiadation
        $request->validate([
            'text'=>'required|string'
        ]);
    // create
   $hint = Hint::create([
        'text'=>$request->text
    ]);
    // response
    return response()->json([
        'message'=>'Hint created successifully',
        'hint'=>$hint
    ]);
    }

    // update hint, only admins can update hints
    public function update(Request $request,Hint $hint)
    {
        // validation
        $request->validate([
            'text'=>'required|string'
        ]);
        // update
        $hint->update([
            'text'=>$request->text
        ]);
        // response
        return response()->json([
            'message'=>'Hint updated successifully',
            'hint'=>$hint
        ]);

    }
    // show hints to users and admins
    public function index()
    {
        $hints=Hint::where('status','active')->latest()->paginate(10);
        return response()->json($hints);
    }
    // soft delete hint, only admins allowed
    public function destroy(Request $request,Hint $hint)
    {
             $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin'){
        abort(403,'Unauthorised action');
    }
    // update
    $hint->update([
        'status'=>'deleted'
    ]);
    // response
    return response()->json([
        'message'=>'Hint deleted successifully',
        'hint'=>$hint
    ]);
    }
}
