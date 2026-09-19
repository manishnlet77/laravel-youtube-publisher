<?php

namespace Manishnlet77\YouTubePublisher;

use Illuminate\Foundation\Application;

class YouTubePublisherManager
{
    public function __construct(protected Application $app)
    {
    }

    public function auth()
    {
        return $this->app->make(\Manishnlet77\YouTubePublisher\Auth\OAuthService::class);
    }
    
    public function videos()
    {
        return $this->app->make(\Manishnlet77\YouTubePublisher\Services\VideoPublisher::class);
    }

    public function shorts()
    {
        return $this->app->make(\Manishnlet77\YouTubePublisher\Services\ShortPublisher::class);
    }

    public function thumbnails()
    {
        return $this->app->make(\Manishnlet77\YouTubePublisher\Services\ThumbnailPublisher::class);
    }

    public function playlists()
    {
        return $this->app->make(\Manishnlet77\YouTubePublisher\Services\PlaylistManager::class);
    }
}
