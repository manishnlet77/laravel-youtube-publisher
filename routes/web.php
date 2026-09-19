<?php

use Illuminate\Support\Facades\Route;

// Sandbox UI Routes for YouTube Publisher
Route::group([
    'prefix' => config('youtube-publisher.sandbox.prefix', 'youtube-publisher'),
    'middleware' => config('youtube-publisher.sandbox.middleware', ['web']),
], function () {
    
    // Check if sandbox is enabled
    if (config('youtube-publisher.sandbox.enabled', false)) {
        
        Route::get('/', function () {
            return view('youtube-publisher::sandbox');
        })->name('youtube-publisher.sandbox');

        // We'll add the auth and upload routes here later
    }
});
