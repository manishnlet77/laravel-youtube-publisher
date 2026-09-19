<div align="center">
    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/09/YouTube_full-color_icon_%282017%29.svg/1024px-YouTube_full-color_icon_%282017%29.svg.png" alt="YouTube Logo" width="120">
    <h1>Laravel YouTube Publisher 🚀</h1>
    <p><i>A powerful, elegant, and production-ready Laravel package for automating YouTube Video and Shorts uploads via the official YouTube Data API v3.</i></p>
    
    <a href="https://packagist.org/packages/manishnlet77/laravel-youtube-publisher"><img src="https://img.shields.io/packagist/v/manishnlet77/laravel-youtube-publisher.svg?style=for-the-badge&logo=laravel" alt="Latest Version on Packagist"></a>
    <a href="https://packagist.org/packages/manishnlet77/laravel-youtube-publisher"><img src="https://img.shields.io/packagist/dt/manishnlet77/laravel-youtube-publisher.svg?style=for-the-badge&color=success" alt="Total Downloads"></a>
    <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.1+-777BB4.svg?style=for-the-badge&logo=php" alt="PHP Version"></a>
    <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-10.x|11.x-FF2D20.svg?style=for-the-badge&logo=laravel" alt="Laravel Version"></a>
</div>

<br>

Perfect for platforms that need to auto-publish videos, manage playlists, set thumbnails, and handle OAuth 2.0 seamlessly.

## ✨ Features 

| Feature | Description |
|---|---|
| 📂 **Bulk Uploads** | Resumable chunked uploads for massive 10GB+ video files safely. |
| 📱 **YouTube Shorts** | Explicit support and auto-tagging for YouTube Shorts API. |
| 🔄 **Auto Token Refresh** | Authenticate once! The package refreshes your token in the background forever. |
| 🎮 **Interactive Sandbox** | A beautiful built-in web UI to test uploads instantly without writing code. |
| 🛠️ **Diagnostic Tools** | Built-in Artisan commands to safely test API quotas and connections. |
| 🖼️ **Playlists & Thumbs** | Fluent API methods to set custom thumbnails and manage your playlists. |

---

# 📖 Zero to End Setup Guide

Follow these steps exactly to go from absolute zero to fully automated YouTube uploads!

### 📦 Step 1: Installation

Install the package via Composer:
```bash
composer require manishnlet77/laravel-youtube-publisher
```

Run the database migrations. This creates a `youtube_credentials` table to securely store your OAuth tokens:
```bash
php artisan migrate
```

*(Optional)* Publish the configuration file:
```bash
php artisan vendor:publish --tag="youtube-publisher-config"
```

---

### ☁️ Step 2: Google Cloud Setup

You must create an OAuth application in Google Cloud to get your API credentials.

1. Go to the [Google Cloud Console](https://console.cloud.google.com/).
2. Create a New Project.
3. Go to **APIs & Services -> Library**, search for **YouTube Data API v3**, and click **Enable**.
4. Go to **Google Auth Platform -> Branding** (or OAuth Consent Screen) and configure the consent screen. 
5. 🛑 **CRITICAL STEP FOR PRODUCTION:** 
   - Go to **Google Auth Platform -> Audience**.
   - Under "Publishing status", click **Publish app** to set it to "In production". 
   - *(If you leave it in "Testing", Google will force your refresh token to expire every 7 days and your auto-uploads will break!)*
   - You do **not** need to submit it for verification if you are only uploading to your own channel.
6. Go to **APIs & Services -> Credentials**.
7. Click **Create Credentials -> OAuth client ID**.
   - Application type: **Web application**
   - Authorized redirect URIs: Add your application's callback URL (e.g., `http://localhost:8000/youtube-publisher/callback` or your production URL).
8. Copy the **Client ID** and **Client Secret**.

---

### ⚙️ Step 3: Environment Configuration

Add your Google credentials to your Laravel `.env` file:

```env
YOUTUBE_PUBLISHER_CLIENT_ID="your-google-client-id"
YOUTUBE_PUBLISHER_CLIENT_SECRET="your-google-client-secret"
YOUTUBE_PUBLISHER_REDIRECT_URI="${APP_URL}/youtube-publisher/callback"

# Enable this to use the built-in UI for testing!
YOUTUBE_PUBLISHER_SANDBOX_ENABLED=true
```

---

### 🔐 Step 4: Authentication & Sandbox

To upload videos, the package needs permission to access your YouTube channel.

1. Ensure `YOUTUBE_PUBLISHER_SANDBOX_ENABLED=true` is in your `.env`.
2. Visit `/youtube-publisher` in your browser (e.g., `http://localhost:8000/youtube-publisher`).
3. Click the **Connect YouTube** button.
4. Log in with your Google account. *(If Google warns you that the app is unverified, click **Advanced -> Go to App (unsafe)**).*
5. You will be redirected back with a success message! Your token is now securely saved in the database.

---

### 🧪 Step 5: Test the Connection

We include a diagnostic Artisan command to ensure your API limits, tokens, and configurations are perfectly set up before you write any code. Run this command in your terminal:

```bash
php artisan youtube:test
```
> **Note:** This will attempt to upload a tiny built-in dummy video as a **Private** video to your channel. If it succeeds, your integration is 100% ready!

---

# 💻 Code Integration

You can now automate uploads anywhere in your Laravel app (Controllers, Jobs, Commands).

### 🎬 Uploading a Video or Short

```php
use Manishnlet77\YouTubePublisher\Facades\YouTubePublisher;
use Manishnlet77\YouTubePublisher\DTO\VideoMetadata;

$absolutePath = storage_path('app/videos/my_video.mp4');

// 1. Prepare the Metadata
$metadata = new VideoMetadata(
    title: "My Awesome Video",       // REQUIRED
    description: "Video desc...",    // REQUIRED
    hashtags: ['laravel', 'php'],    // REQUIRED
    privacy: 'public'                // Optional: 'public', 'private', or 'unlisted'
);

// 2. Upload Normal Video
$response = YouTubePublisher::videos()->upload($absolutePath, $metadata);

// OR Upload YouTube Short
// $response = YouTubePublisher::shorts()->upload($absolutePath, $metadata);

echo "Uploaded! Video ID: " . $response['id'];
```

### 🖼️ Setting a Thumbnail
```php
$thumbnailPath = storage_path('app/images/thumb.jpg');
YouTubePublisher::thumbnails()->set('YOUTUBE_VIDEO_ID', $thumbnailPath);
```

### 📋 Managing Playlists
```php
// Create a playlist
$playlist = YouTubePublisher::playlists()->create(
    title: 'Laravel Tutorials', 
    description: 'Learn Laravel', 
    privacy: 'public'
);

// Add a video to it
YouTubePublisher::playlists()->addVideo($playlist['id'], 'YOUTUBE_VIDEO_ID');
```

---

## 🛑 Troubleshooting & Common Errors

| Error | Cause & Solution |
|---|---|
| `quotaExceeded` | YouTube strictly limits API usage to 10,000 units per day. Uploading one video costs 1,600 units (approx. 6 videos a day). You must wait 24 hours or request a quota increase from Google. |
| `invalid_grant` | Your refresh token expired or was revoked. Visit `/youtube-publisher` and log in again. |
| `401 Unauthorized` | You did not enable the YouTube Data API v3 in your Google Cloud project. |

## 📜 License
MIT License.
