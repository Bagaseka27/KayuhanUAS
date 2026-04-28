<?php

use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

// Endpoint: domain-kamu.com/api/xendit/callback
Route::post('/xendit/callback', [TransaksiController::class, 'handleCallback']);
