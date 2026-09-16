<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use Illuminate\Support\Facades\Mail;

Route::get('/test-email', function () {

    Mail::raw('This is a test email from Laravel.', function ($message) {
        $message->to('aredoivan019@gmail.com')
            ->subject('Laravel Email Test');
    });

    return response()->json([
        'message' => 'Email sent'
    ]);
});