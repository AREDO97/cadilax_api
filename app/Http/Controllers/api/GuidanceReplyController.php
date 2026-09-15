<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\GuidanceReply;
use App\Models\GuidanceRequest;
use App\Models\User;
use App\Notifications\guidanceReplyNotification;
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
        $guidanceReply=GuidanceReply::create([
            'user_id'=>$admin->id,
            'message'=>$request->message,
            'guidance_request_id'=>$guidance->id
        ]);   
     //  $user=$guidance->user;
       $user=User::where('id',$guidance->user_id)->first();
       $user->notify(
            new guidanceReplyNotification($guidanceReply->message)
       );
        // response
        return response()->json([
            'message'=>'Guidance Request Reply sent',
            'guidance_request_reply'=>$guidanceReply,
            'guidance_request'=>$guidance
        ]);
    }
}
