<?php

namespace Manishnlet77\YouTubePublisher\Services;

use Illuminate\Support\Facades\Http;
use Manishnlet77\YouTubePublisher\Auth\OAuthService;
use Exception;

class ThumbnailPublisher
{
    protected string $baseUrl = 'https://www.googleapis.com/upload/youtube/v3/thumbnails/set';

    public function __construct(protected OAuthService $oauth)
    {
    }

    public function set(string $videoId, string $filePath): array
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new Exception("Thumbnail file does not exist or is not readable: {$filePath}");
        }

        $accessToken = $this->oauth->getAccessToken();
        $mimeType = mime_content_type($filePath) ?: 'image/jpeg';
        
        $allowedMimes = ['image/jpeg', 'image/png'];
        if (!in_array($mimeType, $allowedMimes)) {
            throw new Exception("Invalid thumbnail format. Must be JPEG or PNG. Provided: {$mimeType}");
        }

        $fileContent = file_get_contents($filePath);

        $response = Http::withToken($accessToken)
            ->withBody($fileContent, $mimeType)
            ->post($this->baseUrl . '?videoId=' . $videoId);

        if ($response->failed()) {
            throw new Exception('Failed to upload thumbnail: ' . $response->body());
        }

        return $response->json();
    }
}
