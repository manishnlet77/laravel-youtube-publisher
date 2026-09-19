# Laravel YouTube Publisher 🚀

![Laravel Version](https://img.shields.io/badge/Laravel-10.x_|_11.x_|_12.x-red.svg?logo=laravel)
![PHP Version](https://img.shields.io/badge/PHP-%5E8.1-blue.svg?logo=php)
![License](https://img.shields.io/badge/License-MIT-green.svg)

A production-ready, SEO-optimized Laravel Composer package for authenticated YouTube publishing (Video & Shorts) via Google OAuth 2.0. Stop struggling with the heavy official Google API PHP Client and use this elegant, Laravel-native wrapper for the YouTube Data API v3!

## Features 🔥
- **Google OAuth 2.0 Integration**: Seamlessly connect your users' YouTube accounts.
- **Normal Video & YouTube Shorts Upload**: Upload regular videos or Shorts natively.
- **Resumable Uploads**: Handle large video files safely without memory exhaustion.
- **Thumbnail Publishing**: Set custom thumbnails for your uploads.
- **Playlists Management**: Create playlists and add your uploaded videos directly.
- **Database-Ready Credentials**: Securely store user access/refresh tokens.
- **No Extra Dependencies**: Uses Laravel's built-in `Http` facade.

## Installation 📦

1. Install the package via Composer:
```bash
composer require manishnlet77/laravel-youtube-publisher
```

2. Publish the configuration file and migrations:
```bash
php artisan vendor:publish --tag="youtube-publisher-config"
php artisan migrate
```

## Google Cloud Console Setup 🛠️

To use this package, you need to create a project in Google Cloud and enable the YouTube Data API v3.

### Step 1: Create a Project
Go to the [Google Cloud Project Create Page](https://console.cloud.google.com/projectcreate) and create a new project.

### Step 2: Enable the API
Go to the [YouTube Data API v3 Library](https://console.cloud.google.com/apis/library/youtube.googleapis.com) and click **Enable**.

### Step 3: Configure OAuth Consent Screen
Go to the [OAuth Consent Screen](https://console.cloud.google.com/apis/credentials/consent).
- Choose **External** (unless you are a Google Workspace user).
- Fill in the App name, Support email, and Developer contact information.
- Add the `.../auth/youtube.upload` scope.

### Step 4: Create Credentials
Go to **Credentials** -> **Create Credentials** -> **OAuth client ID**.
- Application type: **Web application**
- Authorized redirect URIs: `https://your-domain.com/youtube-publisher/callback` (or your local dev URL).
- Copy your **Client ID** and **Client Secret**.

## Configuration ⚙️

Add your credentials to your Laravel `.env` file:

```env
YOUTUBE_PUBLISHER_CLIENT_ID="your-client-id"
YOUTUBE_PUBLISHER_CLIENT_SECRET="your-client-secret"
YOUTUBE_PUBLISHER_REDIRECT_URI="${APP_URL}/youtube-publisher/callback"
```

## Interactive Sandbox Demo 🎮
Want to test video and shorts uploading instantly without writing any code? We've built an Interactive Sandbox directly into the package!

1. Enable the sandbox in your `.env`:
```env
YOUTUBE_PUBLISHER_SANDBOX_ENABLED=true
```
2. Visit `/youtube-publisher` in your browser (e.g., `http://localhost:8000/youtube-publisher` or `http://192.168.1.5/your-app/youtube-publisher`).
3. Click **Connect YouTube** to authenticate.
4. Use the beautiful UI to upload normal videos or YouTube Shorts directly from your browser!

## Usage 💻

### Authentication
Generate the OAuth URL and redirect the user:
```php
use Manishnlet77\YouTubePublisher\Facades\YouTubePublisher;

return redirect(YouTubePublisher::auth()->getAuthUrl());
```

### Video Uploading
```php
use Manishnlet77\YouTubePublisher\Facades\YouTubePublisher;

$video = YouTubePublisher::videos()->upload(
    file_path: storage_path('app/videos/my_video.mp4'),
    title: 'My Awesome Laravel Video',
    description: 'Uploading via manishnlet77/laravel-youtube-publisher! #laravel #youtube',
    privacy: 'private' // public, private, or unlisted
);

echo "Video ID: " . $video['id'];
```

### Shorts Uploading
Uploading a Short is essentially a video upload that adheres to YouTube's Shorts guidelines (vertical format, <= 60 seconds).
```php
$short = YouTubePublisher::shorts()->upload(
    file_path: storage_path('app/videos/my_short.mp4'),
    title: 'My First Short',
    description: 'This is a short! #shorts'
);
```

## Support & Documentation 📚
Detailed documentation can be found in the `docs/` folder of this repository.

## License 📜
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
