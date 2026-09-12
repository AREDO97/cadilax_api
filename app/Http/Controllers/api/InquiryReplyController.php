<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\InquiryReply;
use App\Notifications\inquiryReplyNotification;
use Illuminate\Http\Request;

class InquiryReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,Inquiry $inquiry)
    {
        // create inquiry reply 
           $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin')
        {
        abort(403,'Unauthorised action');
        }
        // validation
        $request->validate([
            'message'=>'required'
        ]);
        // create reply
        $reply = InquiryReply::create([
            'user_id'=>$admin->id,
            'inquiry_id'=>$inquiry->id,
            'message'=>$request->message
        ]);
        // mark inquiry as replied
        $inquiry->update([
            'is_replied'=>true
        ]);
        // send notification to user
        $user=$inquiry->user;
        $user->notify(
            new inquiryReplyNotification($reply->message)
        );
        // response
        return response()->json([
            'message'=>'Inquiry Reply sent successifully',
            'reply'=>$reply
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
