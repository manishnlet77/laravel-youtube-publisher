<?php

namespace Manishnlet77\YouTubePublisher\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Manishnlet77\YouTubePublisher\Auth\OAuthService;
use Manishnlet77\YouTubePublisher\DTO\VideoMetadata;
use Exception;

class VideoPublisher
{
    protected string $baseUrl = 'https://www.googleapis.com/upload/youtube/v3/videos';

    public function __construct(protected OAuthService $oauth)
    {
    }

    public function upload(string $filePath, VideoMetadata $metadata): array
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new Exception("Video file does not exist or is not readable: {$filePath}");
        }

        $accessToken = $this->oauth->getAccessToken();
        $fileSize = filesize($filePath);
        $mimeType = mime_content_type($filePath) ?: 'video/*';

        // 1. Initialize Resumable Upload
        $initResponse = Http::withToken($accessToken)
            ->withHeaders([
                'X-Upload-Content-Length' => $fileSize,
                'X-Upload-Content-Type' => $mimeType,
            ])
            ->post($this->baseUrl . '?uploadType=resumable&part=snippet,status', $metadata->toArray());

        if ($initResponse->failed()) {
            throw new Exception('Failed to initialize upload: ' . $initResponse->body());
        }

        $uploadUrl = $initResponse->header('Location');

        if (!$uploadUrl) {
            throw new Exception('Did not receive upload URL from YouTube.');
        }

        // 2. Upload the file
        $chunkSize = Config::get('youtube-publisher.publishing.chunk_size', 2097152); // default 2MB
        $fileHandle = fopen($filePath, 'r');
        
        $start = 0;
        $response = null;

        while (!feof($fileHandle)) {
            $chunk = fread($fileHandle, $chunkSize);
            $end = $start + strlen($chunk) - 1;
            
            $response = Http::withBody($chunk, $mimeType)
                ->withHeaders([
                    'Content-Range' => "bytes {$start}-{$end}/{$fileSize}",
                ])
                ->put($uploadUrl);

            // 308 Resume Incomplete means chunk uploaded successfully but not finished
            if ($response->status() !== 308 && $response->failed()) {
                fclose($fileHandle);
                throw new Exception('Failed to upload video chunk: ' . $response->body());
            }

            $start += $chunkSize;
        }

        fclose($fileHandle);

        return $response ? $response->json() : [];
    }
}
