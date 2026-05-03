<?php

use App\Http\Controllers\Api\PollController;
use Illuminate\Support\Facades\Route;

Route::post('/polls', [PollController::class, 'store']);
Route::get('/polls/{shortCode}', [PollController::class, 'show']);
Route::post('/polls/{shortCode}/vote', [PollController::class, 'vote']);