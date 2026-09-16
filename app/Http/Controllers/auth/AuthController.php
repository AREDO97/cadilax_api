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
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Password;
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

    // notify admins
        $admins=User::whereIn('role',['admin','super_admin'])
        ->get();
        foreach ($admins as $admin)
            {
                $admin->notify(
                    new newAccountCreation($user->name)
                );
            }
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
        'password' => 'required',
        'remembere'=>'sometimes|boolean'
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
    // generate remember me token
    $expiresAt=$request->boolean('remember')
    ?  now()->addDays(30)
    : now()->addMinutes(30);

    // 4. Generate plain text token
    $token = $user->createToken('auth-token'
    ,
    [],
    $expiresAt );
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


// google oauth

public function google()
{
    // Add ->stateless() before ->redirect()
    return Socialite::driver('google')->stateless()->redirect();
}

public function googleCallback()
{
    // Add ->stateless() here as well when retrieving the user
    $googleUser = Socialite::driver('google')->stateless()->user();

    // Find or create the user in your database
    $user = User::updateOrCreate([
        'email' => $googleUser->getEmail(),
    ], [
        'name' => $googleUser->getName(),
        'google_id' => $googleUser->getId(),
        'password' => bcrypt(Str::random(16)), // or null if password field allows it
    ]);

    // Generate Sanctum Token for API response
    $token = $user->createToken('auth_token');

    return response()->json([
        'status' => 'success',
        'token' => $token,
        'user' => $user
    ]);
}

// forgot password endpoint
public function forgotPassword(Request $request)
{
    $request->validate([
        'email' => ['required', 'email'],
    ]);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    if ($status === Password::RESET_LINK_SENT) {
        return response()->json([
            'message' => 'Password reset link sent successfully.'
        ]);
    }

    return response()->json([
        'message' => 'Unable to send password reset link.'
    ], 422);
}
}




