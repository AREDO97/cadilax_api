<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;

class PlatformSettingsController extends Controller
{
    // create percentage
    public function create(Request $request)
    {
        // enforce admins only
        $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin'){
        abort(403,'Unauthorised action');
    }
        // valiadtion
        $data = $request->validate([
            'percentage'=>'required'
        ]);
        // create
        $percentage=PlatformSetting::create($data);
        // response
        return response()->json([
            'message'=>'New tax configuration set',
            'percentage'=>$percentage
        ]);
    }
    // update percentage 
    public function update(Request $request,PlatformSetting $percentage)
    {
          // enforce admins only
        $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin'){
        abort(403,'Unauthorised action');
    }
            // valiadtion
        $request->validate([
            'percentage'=>'required'
        ]);
        // update
        $percentage->update([
            'percentage'=>$request->percentage
        ]);
        // response
        return response()->json([
            'message'=>'Tax percentage updated successifully',
            'percentage'=>$percentage
        ]);
    }
    // delete configuation
    public function destroy(Request $request,PlatformSetting $percentage)
    {
          // enforce admins only
        $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin'){
        abort(403,'Unauthorised action');
    }
        $percentage->update([
            'is_active'=>false
        ]);
        // response
        return response()->json([
            'message'=>'configuration percentage inactivated',
            'percentage'=>$percentage
        ]);
    }
    // current percentage
    public function index()
    {
        $percenatage=PlatformSetting::where('is_active',true)->first();
        return response()->json($percenatage);
    }
}
