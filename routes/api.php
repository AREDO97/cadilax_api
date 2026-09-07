<?php

use App\Http\Controllers\api\AuditLogsController;
use App\Http\Controllers\api\UserController;
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