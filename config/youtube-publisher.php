<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google API Credentials
    |--------------------------------------------------------------------------
    |
    | Set your Google API credentials here. These are required for the
    | OAuth 2.0 authorization flow.
    |
    */
    'client_id' => env('YOUTUBE_PUBLISHER_CLIENT_ID'),
    'client_secret' => env('YOUTUBE_PUBLISHER_CLIENT_SECRET'),
    
    /*
    |--------------------------------------------------------------------------
    | OAuth Redirect URI
    |--------------------------------------------------------------------------
    |
    | This is the callback URI where Google will redirect the user after
    | they have authorized your application.
    |
    */
    'redirect_uri' => env('YOUTUBE_PUBLISHER_REDIRECT_URI', env('APP_URL') . '/youtube-publisher/callback'),

    /*
    |--------------------------------------------------------------------------
    | OAuth Scopes
    |--------------------------------------------------------------------------
    |
    | The default scopes required for uploading videos and managing playlists.
    |
    */
    'scopes' => [
        'https://www.googleapis.com/auth/youtube.upload',
        'https://www.googleapis.com/auth/youtube', // Broader scope needed for playlists/thumbnails if required
    ],

    /*
    |--------------------------------------------------------------------------
    | Sandbox UI
    |--------------------------------------------------------------------------
    |
    | Enable or disable the demo Sandbox UI for testing the publishing features.
    |
    */
    'sandbox' => [
        'enabled' => env('YOUTUBE_PUBLISHER_SANDBOX_ENABLED', false),
        'middleware' => ['web'],
        'prefix' => 'youtube-publisher',
    ],

    /*
    |--------------------------------------------------------------------------
    | Publishing Settings
    |--------------------------------------------------------------------------
    */
    'publishing' => [
        'resumable_upload' => true,
        'chunk_size' => 1024 * 1024 * 2, // 2MB chunk size for resumable uploads
    ],
];
