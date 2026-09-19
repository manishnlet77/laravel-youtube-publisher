<?php

namespace Manishnlet77\YouTubePublisher\Facades;

use Illuminate\Support\Facades\Facade;

class YouTubePublisher extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'youtube-publisher';
    }
}
