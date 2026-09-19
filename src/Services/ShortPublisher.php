<?php

namespace Manishnlet77\YouTubePublisher\Services;

use Manishnlet77\YouTubePublisher\DTO\VideoMetadata;
use Exception;

class ShortPublisher
{
    public function __construct(protected VideoPublisher $videoPublisher)
    {
    }

    /**
     * Upload a YouTube Short.
     * Note: YouTube automatically classifies videos as Shorts if they are under 60 seconds 
     * and have a vertical aspect ratio. 
     */
    public function upload(string $filePath, VideoMetadata $metadata): array
    {
        // Add #shorts to hashtags if not present
        if (!in_array('shorts', $metadata->hashtags) && !in_array('#shorts', $metadata->hashtags)) {
            $metadata->hashtags[] = 'shorts';
        }

        return $this->videoPublisher->upload($filePath, $metadata);
    }
}
