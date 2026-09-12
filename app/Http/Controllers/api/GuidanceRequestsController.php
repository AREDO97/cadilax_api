<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\GuidanceRequest;
use App\Notifications\guidanceGuiandanceInquiry;
use Illuminate\Http\Request;
use App\Models\User;

class GuidanceRequestsController extends Controller
{
    // create request
    public function create(Request $request)
    {
        $user=$request->user();
        // valiadtion
        $request->validate([
            'category'=>'required',
            'message'=>'required'
        ]);
        // create request
        $guidance_request=GuidanceRequest::create([
            'user_id'=>$user->id,
            'message'=>$request->message,
            'category'=>$request->category
        ]);

         // notify admins
        $admins=User::whereIn('role',['admin','super_admin'])->get();
        foreach($admins as $super)
            {
                $super->notify(
                new guidanceGuiandanceInquiry(
                    "user",
                    $guidance_request->category
                )
                );
            }
        // response
        return response()->json([
            'message'=>'Message sent , check your notifications for the reply',
            'guidance_request'=>$guidance_request
        ]);
    }

    // view guidance requests
    public function index(Request $request)
    {
            $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin')
        {
        abort(403,'Unauthorised action');
        }
        // all guidance requests
        $guidance_requests=GuidanceRequest::latest()->paginate(10);
        // response
        return response()->json($guidance_requests);
    }
    // delete requests
    public function destroy(Request $request,GuidanceRequest $guidance)
    {
                   $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin')
        {
        abort(403,'Unauthorised action');
        }
        // delete
        $guidance->delete();
        // response
        return response()->json([
            'message'=>'Guidance request deleted'
        ]);
    }
}
