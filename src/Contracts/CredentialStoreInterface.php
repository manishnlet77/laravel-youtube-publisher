<?php

namespace Manishnlet77\YouTubePublisher\Contracts;

interface CredentialStoreInterface
{
    public function getAccessToken(): ?string;
    
    public function getRefreshToken(): ?string;
    
    public function saveTokens(string $accessToken, ?string $refreshToken, int $expiresIn): void;
    
    public function clearTokens(): void;
    
    public function hasValidAccessToken(): bool;
}
