<?php

namespace Manishnlet77\YouTubePublisher\Services;

use Illuminate\Support\Facades\Http;
use Manishnlet77\YouTubePublisher\Auth\OAuthService;
use Exception;

class PlaylistManager
{
    public function __construct(protected OAuthService $oauth)
    {
    }

    public function create(string $title, string $description = '', string $privacy = 'private'): array
    {
        $accessToken = $this->oauth->getAccessToken();

        $data = [
            'snippet' => [
                'title' => $title,
                'description' => $description,
            ],
            'status' => [
                'privacyStatus' => $privacy,
            ]
        ];

        $response = Http::withToken($accessToken)
            ->post('https://www.googleapis.com/youtube/v3/playlists?part=snippet,status', $data);

        if ($response->failed()) {
            throw new Exception('Failed to create playlist: ' . $response->body());
        }

        return $response->json();
    }

    public function addVideo(string $playlistId, string $videoId): array
    {
        $accessToken = $this->oauth->getAccessToken();

        $data = [
            'snippet' => [
                'playlistId' => $playlistId,
                'resourceId' => [
                    'kind' => 'youtube#video',
                    'videoId' => $videoId,
                ]
            ]
        ];

        $response = Http::withToken($accessToken)
            ->post('https://www.googleapis.com/youtube/v3/playlistItems?part=snippet', $data);

        if ($response->failed()) {
            throw new Exception('Failed to add video to playlist: ' . $response->body());
        }

        return $response->json();
    }
}
