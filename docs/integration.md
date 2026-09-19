# Integration Guide: How to Use in Your Laravel Project

This guide explains how to integrate `manishnlet77/laravel-youtube-publisher` directly into your Laravel application's controllers, jobs, or commands.

## 1. Authentication Flow

Before you can upload anything, the user (or your application's admin account) must authorize the application via Google OAuth.

### Step 1: Create Auth Routes
In your `routes/web.php`:

```php
use Manishnlet77\YouTubePublisher\Facades\YouTubePublisher;
use Illuminate\Http\Request;

Route::get('/youtube/login', function () {
    // Generates the Google OAuth URL
    return redirect(YouTubePublisher::auth()->getAuthUrl());
});

Route::get('/youtube/callback', function (Request $request) {
    if ($request->has('code')) {
        // Exchanges the code for an Access & Refresh token and saves it to the Database
        YouTubePublisher::auth()->handleCallback($request->get('code'));
        return "Authentication Successful!";
    }
    return "Authentication Failed!";
});
```

## 2. Uploading Videos & Shorts

Once authenticated, you can upload videos anywhere in your Laravel project (e.g., inside a Controller or a queued Job).

### Required vs Optional Fields

When building the `VideoMetadata` object, certain fields are strictly required for public videos to ensure high-quality, SEO-friendly uploads.

**Required Fields:**
* `title` (string)
* `description` (string)
* `tags` OR `hashtags` (array)
* `video_file_path` (string) - Passed directly to the upload method.

**Optional Fields:**
* `privacy` (string: `public`, `private`, `unlisted`) - Defaults to `public`
* `madeForKids` (bool) - Defaults to `false`
* `categoryId` (string) - YouTube Category ID (e.g., '22' for People & Blogs)
* `publishAt` (string) - ISO 8601 datetime string for scheduling

### Example: Controller Upload

```php
namespace App\Http\Controllers;

use Manishnlet77\YouTubePublisher\Facades\YouTubePublisher;
use Manishnlet77\YouTubePublisher\DTO\VideoMetadata;
use Illuminate\Http\Request;

class YouTubeController extends Controller
{
    public function uploadVideo(Request $request)
    {
        // 1. Validate the incoming video
        $request->validate([
            'video' => 'required|file|mimetypes:video/mp4'
        ]);

        // 2. Store the video locally
        $path = $request->file('video')->store('youtube-uploads');
        $absolutePath = storage_path('app/' . $path);

        // 3. Build Metadata (Required and Optional fields)
        $metadata = new VideoMetadata(
            title: "My Awesome Video",       // REQUIRED
            description: "Video desc...",    // REQUIRED
            hashtags: ['laravel', 'php'],    // REQUIRED
            privacy: 'public',               // Optional (Defaults to public)
            madeForKids: false               // Optional
        );

        // 4. Upload!
        try {
            $response = YouTubePublisher::videos()->upload($absolutePath, $metadata);
            
            // Or for a Short:
            // $response = YouTubePublisher::shorts()->upload($absolutePath, $metadata);

            return response()->json([
                'success' => true,
                'youtube_id' => $response['id']
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
```

## 3. Thumbnails & Playlists (Optional)

You can further enhance your uploads using the optional Thumbnail and Playlist APIs.

### Setting a Thumbnail
```php
$videoId = 'YOUTUBE_VIDEO_ID';
$thumbnailPath = storage_path('app/images/thumb.jpg');

YouTubePublisher::thumbnails()->set($videoId, $thumbnailPath);
```

### Creating & Adding to a Playlist
```php
// 1. Create a playlist
$playlist = YouTubePublisher::playlists()->create(
    title: 'My Laravel Tutorials', 
    description: 'A collection of tutorials', 
    privacy: 'public'
);

$playlistId = $playlist['id'];

// 2. Add an uploaded video to it
YouTubePublisher::playlists()->addVideo($playlistId, 'YOUTUBE_VIDEO_ID');
```
