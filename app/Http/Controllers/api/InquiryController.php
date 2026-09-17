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
     * view all user inquiries.
     * admins only allowed.
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
     * create an inquiry.
     * all users allowed to send an inquiry
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
     * Display the sinquiry statistics.
     */
    public function show(Request $request)
    {
            // enforce admins
         $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin')
        {
        abort(403,'Unauthorised action');
        }
        // inquiry stats
        $totalInquiries=Inquiry::all()->count();
        $inquiriesToday=Inquiry::whereDate('created_at',today())->count();
        $totalRepliedInquiries=Inquiry::whereDate('created_at',today())
        ->where('is_replied',true)->count();
        // return response
        return response()->json([
            'total_inquiries'=>$totalInquiries,
            'total_inquiries_today'=>$inquiriesToday,
            'total_replied_inquiries_today'=>$totalRepliedInquiries
        ]);
    }

 /**
     * Delete an inquiry
     *
     * Permanently remove the specified inquiry record from the database.
     * admins only allowed to perform action
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
