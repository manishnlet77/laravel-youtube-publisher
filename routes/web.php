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
            $hasToken = app(\Manishnlet77\YouTubePublisher\Contracts\CredentialStoreInterface::class)->hasValidAccessToken();
            return view('youtube-publisher::sandbox', compact('hasToken'));
        })->name('youtube-publisher.sandbox');

        Route::get('/auth', function () {
            $url = app('youtube-publisher')->auth()->getAuthUrl();
            return redirect($url);
        })->name('youtube-publisher.auth');

        Route::get('/callback', function (\Illuminate\Http\Request $request) {
            if ($request->has('code')) {
                app('youtube-publisher')->auth()->handleCallback($request->get('code'));
                return redirect()->route('youtube-publisher.sandbox')->with('success', 'YouTube account connected successfully!');
            }
            return redirect()->route('youtube-publisher.sandbox')->with('error', 'Authorization failed.');
        });
        
        Route::post('/test-upload', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'video' => 'required|file|mimetypes:video/mp4,video/quicktime|max:50000',
                'title' => 'required|string|max:100',
                'type' => 'required|in:video,short'
            ]);

            try {
                $path = $request->file('video')->store('youtube-sandbox');
                $fullPath = storage_path('app/' . $path);

                $metadata = new \Manishnlet77\YouTubePublisher\DTO\VideoMetadata(
                    title: $request->get('title'),
                    description: "Uploaded from Laravel Sandbox! #test",
                    hashtags: ['test', 'sandbox'],
                    privacy: 'public'
                );

                if ($request->get('type') === 'short') {
                    $response = app('youtube-publisher')->shorts()->upload($fullPath, $metadata);
                } else {
                    $response = app('youtube-publisher')->videos()->upload($fullPath, $metadata);
                }

                return redirect()->route('youtube-publisher.sandbox')->with('success', 'Uploaded successfully! Video ID: ' . ($response['id'] ?? 'Unknown'));

            } catch (\Exception $e) {
                return redirect()->route('youtube-publisher.sandbox')->with('error', 'Upload failed: ' . $e->getMessage());
            }
        })->name('youtube-publisher.upload');
    }
});
