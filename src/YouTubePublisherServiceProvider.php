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
        $this->publishes([
            __DIR__ . '/../config/youtube-publisher.php' => config_path('youtube-publisher.php'),
        ], 'youtube-publisher-config');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'youtube-publisher-migrations');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'youtube-publisher');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Register the Artisan commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Manishnlet77\YouTubePublisher\Commands\TestUploadCommand::class,
            ]);
        }
    }
}
