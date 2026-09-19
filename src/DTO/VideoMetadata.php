<?php

namespace Manishnlet77\YouTubePublisher\DTO;

class VideoMetadata
{
    public function __construct(
        public string $title,
        public string $description = '',
        public array $tags = [],
        public array $hashtags = [],
        public string $privacy = 'public',
        public bool $madeForKids = false,
        public ?string $categoryId = null,
        public ?string $publishAt = null
    ) {
        $this->validate();
    }

    protected function validate(): void
    {
        if (empty(trim($this->title))) {
            throw new \InvalidArgumentException('Video title is required and cannot be empty.');
        }

        if (empty(trim($this->description))) {
            throw new \InvalidArgumentException('Video description is required before publishing publicly.');
        }

        if (empty($this->tags) && empty($this->hashtags)) {
            throw new \InvalidArgumentException('At least one tag or hashtag is required before publishing publicly.');
        }
    }

    public function getProcessedDescription(): string
    {
        $desc = $this->description;
        
        if (!empty($this->hashtags)) {
            $formattedHashtags = array_map(function ($tag) {
                return str_starts_with($tag, '#') ? $tag : '#' . $tag;
            }, $this->hashtags);
            
            $desc .= "\n\n" . implode(' ', $formattedHashtags);
        }
        
        return $desc;
    }

    public function toArray(): array
    {
        return [
            'snippet' => array_filter([
                'title' => $this->title,
                'description' => $this->getProcessedDescription(),
                'tags' => $this->tags,
                'categoryId' => $this->categoryId,
            ]),
            'status' => array_filter([
                'privacyStatus' => $this->privacy,
                'publishAt' => $this->publishAt,
                'selfDeclaredMadeForKids' => $this->madeForKids,
            ]),
        ];
    }
}
