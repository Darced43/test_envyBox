<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app'); 
});

Route::get('/{any}', function () {
    return view('app');
})
->where('any', '^(?!api|_ignition|vendor|sanctum|build|favicon).*$') 
->withoutMiddleware(['auth', 'verified', 'web']); 
