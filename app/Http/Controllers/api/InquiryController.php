<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\User;
use App\Notifications\inquiryNotification;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // enforce admins
         $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin')
        {
        abort(403,'Unauthorised action');
        }
        // all inquiries
        $inquiries=Inquiry::latest()->paginate(10);
        // response
        return response()->json($inquiries);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // create inquiry
  
        $user=$request->user();
        $request->validate([
            'category'=>'required',
            'subject'=>'max:200',
            'message'=>'string|max:255'
        ]);
        //create inquiry
        $inquiry=Inquiry::create([
            'user_id'=>$user->id,
            'category'=>$request->category,
            'subject'=>$request->subject,
            'message'=>$request->subject
        ]);
        // notification
        $admins=User::whereIn('role',['admin','super_admin'])->get();
        foreach ($admins as $admin)
            {
                $admin->notify(
                    new inquiryNotification($user->name,$inquiry->category)
                );
            }
        // response
        return response()->json([
            'message'=>'Your inquiry has been sent, check notifications to see reply',
            'inquiry'=>$inquiry
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
    public function destroy(Request $request ,Inquiry $inquiry)
    {
        // soft delete inquiry
           // enforce admins
         $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin')
        {
        abort(403,'Unauthorised action');
        }
        // delete inquiry
        $inquiry->delete();
        // reponse
        return response()->json([
            'message'=>'inquiry deleted successifully'
        ]);
    }
    // replied inquiries
    public function repliedInquiry(Request $request)
    {
        $repliedInquiries=Inquiry::where('is_replied',true)->latest()->paginate(10);
        // response 
        return response()->json($repliedInquiries);
    }
}
