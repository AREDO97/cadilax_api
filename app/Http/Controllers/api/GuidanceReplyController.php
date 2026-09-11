<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\GuidanceReply;
use App\Models\GuidanceRequest;
use App\Models\User;
use App\Notifications\guidanceGuiandanceInquiry;
use Illuminate\Http\Request;

class GuidanceReplyController extends Controller
{
    // create reply
    public function create(Request $request,GuidanceRequest $guidance)
    {
        // enforce admins only  
        $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin')
        {
        abort(403,'Unauthorised action');
        }
        // validation
        $request->validate([
            'message'=>'required'
        ]);
        $gudance_reply=GuidanceReply::create([
            'user_id'=>$admin->id,
            'message'=>$request->message,
            'guidance_request_id'=>$guidance->id
        ]);   
       
        // response
        return response()->json([
            'message'=>'Guidance Request Reply sent',
            'guidance_request_reply'=>$gudance_reply
        ]);
    }
}
