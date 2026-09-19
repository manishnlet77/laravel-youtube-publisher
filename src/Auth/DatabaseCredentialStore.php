<?php

namespace Manishnlet77\YouTubePublisher\Auth;

use Manishnlet77\YouTubePublisher\Contracts\CredentialStoreInterface;
use Manishnlet77\YouTubePublisher\Models\YouTubeCredential;
use Illuminate\Support\Carbon;

class DatabaseCredentialStore implements CredentialStoreInterface
{
    public function getAccessToken(): ?string
    {
        $cred = YouTubeCredential::first();
        return $cred ? $cred->access_token : null;
    }
    
    public function getRefreshToken(): ?string
    {
        $cred = YouTubeCredential::first();
        return $cred ? $cred->refresh_token : null;
    }
    
    public function saveTokens(string $accessToken, ?string $refreshToken, int $expiresIn): void
    {
        $cred = YouTubeCredential::first() ?? new YouTubeCredential();
        
        $cred->access_token = $accessToken;
        if ($refreshToken) {
            $cred->refresh_token = $refreshToken;
        }
        $cred->expires_at = Carbon::now()->addSeconds($expiresIn);
        
        $cred->save();
    }
    
    public function clearTokens(): void
    {
        YouTubeCredential::truncate();
    }
    
    public function hasValidAccessToken(): bool
    {
        $cred = YouTubeCredential::first();
        return $cred && $cred->expires_at && $cred->expires_at->isFuture();
    }
}
