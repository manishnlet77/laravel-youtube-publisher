<?php

namespace Manishnlet77\YouTubePublisher\Auth;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Manishnlet77\YouTubePublisher\Contracts\CredentialStoreInterface;
use Exception;

class OAuthService
{
    public function __construct(protected CredentialStoreInterface $store)
    {
    }

    public function getAuthUrl(string $state = null): string
    {
        $clientId = Config::get('youtube-publisher.client_id');
        $redirectUri = Config::get('youtube-publisher.redirect_uri');
        $scopes = implode(' ', Config::get('youtube-publisher.scopes', []));

        if (!$clientId || !$redirectUri) {
            throw new Exception("YouTube Publisher credentials are not configured properly.");
        }

        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $scopes,
            'access_type' => 'offline',
            'prompt' => 'consent',
        ];

        if ($state) {
            $params['state'] = $state;
        }

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    public function handleCallback(string $code): array
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => Config::get('youtube-publisher.client_id'),
            'client_secret' => Config::get('youtube-publisher.client_secret'),
            'redirect_uri' => Config::get('youtube-publisher.redirect_uri'),
            'grant_type' => 'authorization_code',
            'code' => $code,
        ]);

        if ($response->failed()) {
            throw new Exception('Failed to exchange authorization code for tokens: ' . $response->body());
        }

        $data = $response->json();
        
        $this->store->saveTokens(
            $data['access_token'],
            $data['refresh_token'] ?? null,
            $data['expires_in']
        );

        return $data;
    }

    public function getAccessToken(): string
    {
        if (!$this->store->hasValidAccessToken()) {
            $this->refreshToken();
        }

        return $this->store->getAccessToken();
    }

    public function refreshToken(): void
    {
        $refreshToken = $this->store->getRefreshToken();
        
        if (!$refreshToken) {
            throw new Exception("No refresh token available. User must re-authenticate.");
        }

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => Config::get('youtube-publisher.client_id'),
            'client_secret' => Config::get('youtube-publisher.client_secret'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);

        if ($response->failed()) {
            throw new Exception('Failed to refresh access token: ' . $response->body());
        }

        $data = $response->json();
        
        $this->store->saveTokens(
            $data['access_token'],
            null, // Google doesn't always return a new refresh token
            $data['expires_in']
        );
    }
}
