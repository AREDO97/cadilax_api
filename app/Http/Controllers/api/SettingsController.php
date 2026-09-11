<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    // settings 
    public function updatePassword(Request $request)
    {

        $request->validate([
            'current_password'=>'required',
            'password'=>'required|confirmed'
        ]);
        $user=$request->user();
        if(! Hash::check($request->current_password,$user->password))
            {
                return response()->json([
                    'message'=>'incorrect password provided'
                ]);
            }
        // when current password is correct
        $user->update([
           'password'=>Hash::make($request->password)
        ]);

        // audit log
        AuditLog::Log(
            $user->id,
            'Password Update',
            $user->name.' updated his password'
        );
        return response()->json([
            'message'=>'password updated successifully'
        ]);
    }

    // username and email
    public function updateUserInfo(Request $request)
    {
        $user=$request->user();
        $request->validate([
    'email' => 'email|unique:users,email,' . $user->id,
    'name' => 'string',
     ]);
       // update user info
        $user->update([
            'name'=>$request->name ?? $user->name,
            'email'=>$request->email ?? $user->email
        ]);

        return response()->json([
            'message'=>'Personal information updated',
            'new_name'=>$user->name,
            'new_email'=>$user->email
        ]);
    }
    // delete account
    public function deleteAccount(Request $request)
    {
        $user=$request->user();
        if(! Hash::check($request->password,$user->password))
            {
            return response()->json([
            'message'=>'Invalid password , try again'
            ]);
            }
            // delete account

 // log action
        AuditLog::Log(
            $user->id,
            'Account Deletion',
            $user->name.' deleted account'
        );
        
        $user->profile()->delete();
       // $user->logs()->delete();
        $user->projects()->delete();
       // $user->events()->delete();
       // $user->comments()->delete();
       // $user->likes()->delete();
        $user->delete();

        return response()->json([
            'message'=>'Account deleted succesifully'
        ]);
    }
  
}
