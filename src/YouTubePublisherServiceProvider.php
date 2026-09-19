<?php

namespace Manishnlet77\YouTubePublisher;

use Illuminate\Support\ServiceProvider;

class YouTubePublisherServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/youtube-publisher.php', 'youtube-publisher');
        
        $this->app->bind(
            \Manishnlet77\YouTubePublisher\Contracts\CredentialStoreInterface::class,
            \Manishnlet77\YouTubePublisher\Auth\DatabaseCredentialStore::class
        );

        $this->app->singleton('youtube-publisher', function ($app) {
            return new YouTubePublisherManager($app);
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/youtube-publisher.php' => config_path('youtube-publisher.php'),
            ], 'youtube-publisher-config');

            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
        
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'youtube-publisher');
    }
}
