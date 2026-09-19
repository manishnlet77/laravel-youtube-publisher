<?php

namespace Manishnlet77\YouTubePublisher\Models;

use Illuminate\Database\Eloquent\Model;

class YouTubeCredential extends Model
{
    protected $table = 'youtube_credentials';
    
    protected $fillable = [
        'channel_id',
        'access_token',
        'refresh_token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
    ];
}
