<?php

use App\Http\Controllers\api\AuditLogsController;
use App\Http\Controllers\api\GameChallengerController;
use App\Http\Controllers\api\GameController;
use App\Http\Controllers\api\HintController;
use App\Http\Controllers\api\StakeController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\WalletController;
use App\Http\Controllers\auth\authController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// register user
Route::post('/register',[authController::class,'register'])->middleware('throttle:3,1')
->name('register');
// login user
Route::post('/login',[authController::class,'login'])->middleware('throttle:3,1')
->name('login');
// log out endpoint
Route::post('/logout',[authController::class,'logout'])->middleware('auth:sanctum')
->name('logout');

// user management
// all users
Route::get('/users',[UserController::class,'index'])
->middleware('auth:sanctum')->name('view users');
Route::get('/userActivity',[UserController::class,'userActivity'])
->middleware('auth:sanctum')->name('user activity');
Route::get('/user/{user}',[UserController::class,'oneUser'])
->middleware('auth:sanctum')->name('view single user');
// update
Route::patch('/users/update/{user}',[UserController::class,'update'])
->middleware('auth:sanctum')->name('update user info');
// only admin delete and update role
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->group(function () {
    // view suspended
    Route::patch('/users/suspend/{user}', [UserController::class, 'softDelete'])->name('suspend user');
    Route::patch('/users/create_admin/{user}', [UserController::class, 'makeAdmin'])->name('make admin');
    Route::patch('/users/demote_admin/{user}', [UserController::class, 'demoteAdmin'])->name('demote admin');
    //viewSuspended
    Route::get('/users/suspended', [UserController::class, 'viewSuspended'])->name('suspended users');
    // unsuspend user
     Route::patch('/users/unsuspend/{user}', [UserController::class, 'unsuspend'])->name('unsuspend user');
    
});

// audit logs management
// view logs
Route::get('/logs',[AuditLogsController::class,'index'])
->middleware(['auth:sanctum','role:admin,super_admin'])->name('all logs');
// logs management
Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
// delete one log
Route::delete('/log/{log}/delete',[AuditLogsController::class,'destroy'])->name('delete single log');
// delete old logs
Route::delete('/logs/delete',[AuditLogsController::class,'clearAll'])->name('delete old logs');
});

// hints management
Route::middleware(['auth:sanctum','role:admin,super_admin'])->group(function (){
// create hint
Route::post('/hint/create',[HintController::class,'create'])->name('create hint');
// update hint
Route::put('/hint/{hint}/update',[HintController::class,'update'])->name('update hint');
// soft delete hint
Route::delete('/hint/{hint}/delete',[HintController::class,'destroy'])
->name('delete hint');
});
// show hints
Route::get('/hints',[HintController::class,'index'])->middleware('auth:sanctum')
->name('show hints');

// games management
Route::middleware(['auth:sanctum'])->group(function (){
// create game
Route::post('/game/create',[GameController::class,'create'])->name('create game');
// cancel game
Route::put('/game/{game}/cancel',[GameController::class,'cancel'])->name('cancel game');
// view open games
Route::get('/open/games',[GameController::class,'index'])->name('open games');
});

// game challenger management
Route::middleware('auth:sanctum')->group(function () {
// challenge game
Route::post('/game/{game}/challenge',[GameChallengerController::class,'challengeGame'])
->name('challenge game');
});

// stakes management
Route::middleware(['auth:sanctum','role:admin,super_admin'])->group(function (){
// create stake
Route::post('/stake/create',[StakeController::class,'create']);
// update stake
Route::put('/stake/{stake}/update',[StakeController::class,'update']);
// delete stakes
Route::delete('/stake/{stake}/delete',[StakeController::class,'destroy']);

});
// view stakes
Route::get('/stakes',[StakeController::class,'index'])
->middleware('auth:sanctum');

// wallet management
Route::middleware(['auth:sanctum'])->group(function () {
// create wallet
Route::post('/wallet/create',[WalletController::class,'create']);
});