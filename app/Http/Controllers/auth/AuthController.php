<?php

namespace App\Http\Controllers\auth;
use App\Models\User;
use App\Models\AuditLog;
use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Profile;
use App\Models\Wallet;
use App\Notifications\newAccountCreation;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // create user
public function register(Request $request)
{
    //  Validation
    $request->validate([
        'name'     => ['required', 'string', 'max:255'],
        'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'min:6'], 
    ]);

    // Insert User
    $user = User::create([
        'name'     => $request->name,
        'email'    => strtolower(trim($request->email)),
        'password' => Hash::make($request->password)
    ]);

    // create wallet
    $wallet = Wallet::create([
            'user_id'=>$user->id,
            'balance'=>0000.00
    ]);
    // Token 
    $token = $user->createToken('auth-token');
    // log action
    // audit log
    AuditLog::Log(
        $user->id,
        'Account Creation',
        $user->name . ' created an account successfully'
    );

// response
    return response()->json([
        'message' => 'Account created successfully',
        'user'    => $user,
        'token'   => $token,
    ], 201);
}
    // login
    public function login(Request $request)
{
    // 1. Validation
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    // 2. Fetch user from DB
// 1. Fetch user by email only
$user = User::where('email', $request->email)->first();

// 2. Check if user exists
if (!$user) {
    return response()->json([
        'message' => 'Invalid credentials'
    ], 401);
}

// 3. Check status
if ($user->status !== 'active') {
    return response()->json([
        'message' => 'Account suspended'
    ], 403);
}

// 4. Proceed to check password / issue token...

    // 3. Check existence AND verify password using OR (||)
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Invalid email or password'
        ], 401);
    }

    // 4. Generate plain text token
    $token = $user->createToken('auth-token')->plainTextToken;
        // audit log
    AuditLog::Log(
        $user->id,
        'Login into Account',
        $user->name . ' logged into account successfully'
    );
    //response
    return response()->json([
        'token' => $token,
        'user'  => $user
    ], 200);
}
    // logout logic
    public function logout(Request $request)
{
    // Revoke the specific token that was used to authenticate this request
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logged out successfully'
    ], 200);
}
}
