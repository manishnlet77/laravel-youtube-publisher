<?php

namespace Manishnlet77\YouTubePublisher\Commands;

use Illuminate\Console\Command;
use Manishnlet77\YouTubePublisher\Facades\YouTubePublisher;
use Manishnlet77\YouTubePublisher\DTO\VideoMetadata;
use Manishnlet77\YouTubePublisher\Contracts\CredentialStoreInterface;
use Exception;

class TestUploadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests the YouTube Publisher package by uploading a small sample video safely to ensure API works.';

    /**
     * Execute the console command.
     */
    public function handle(CredentialStoreInterface $store)
    {
        $this->info("===============================================");
        $this->info("📹 YouTube Publisher - Connection Test");
        $this->info("===============================================");

        if (!$store->hasValidAccessToken() && !$store->getRefreshToken()) {
            $this->error("\n❌ Error: You are not authenticated!");
            $this->warn("Solution: Open your browser and visit /youtube-publisher to log in via the Sandbox UI first.");
            return Command::FAILURE;
        }

        $videoPath = realpath(__DIR__ . '/../../tests/Fixtures/test.mp4');

        if (!$videoPath || !file_exists($videoPath)) {
            $this->error("\n❌ Error: Built-in test video not found at tests/Fixtures/test.mp4");
            return Command::FAILURE;
        }

        $this->info("\n✅ Authentication detected in database.");
        $this->info("Uploading a test video as 'Private' to check API quota and connection...");

        $metadata = new VideoMetadata(
            title: "Automated Package Test - " . now()->format('Y-m-d H:i'),
            description: "If this uploaded, your laravel-youtube-publisher is perfectly configured! #test",
            hashtags: ['test', 'laravel', 'youtubepublisher'],
            privacy: 'private' // Forced to private so users don't accidentally spam subscribers
        );

        try {
            $response = YouTubePublisher::videos()->upload($videoPath, $metadata);
            
            $this->info("\n🎉 SUCCESS! Upload completed perfectly.");
            $this->info("Video ID: " . ($response['id'] ?? 'Unknown'));
            $this->info("You can verify this in YouTube Studio -> Content.");

            return Command::SUCCESS;

        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->error("\n❌ UPLOAD FAILED: Something went wrong.");
            $this->error($message);
            
            // Easy Problem Solving Hints
            $this->info("\n🛠️ Easy Solve Guide:");
            
            if (str_contains(strtolower($message), 'quota')) {
                $this->warn("- Your Google Cloud API has exceeded its daily 10,000 unit quota.");
                $this->warn("- Solution: Wait 24 hours for quota to reset, or request an extension in Google Cloud Console.");
            } elseif (str_contains(strtolower($message), 'invalid_grant')) {
                $this->warn("- Your refresh token has expired or was revoked.");
                $this->warn("- Solution: Visit your /youtube-publisher Sandbox route and log in again.");
            } elseif (str_contains(strtolower($message), 'unauthorized') || str_contains(strtolower($message), '401')) {
                $this->warn("- You might not have the correct scopes enabled, or the token is corrupted.");
                $this->warn("- Solution: Delete the row in the `youtube_credentials` table and log in again.");
            } else {
                $this->warn("- Please check your internet connection, file permissions, or ensure the YouTube API is enabled in Google Cloud.");
            }

            return Command::FAILURE;
        }
    }
}
