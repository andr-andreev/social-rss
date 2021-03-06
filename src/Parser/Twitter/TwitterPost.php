<?php
declare(strict_types=1);


namespace SocialRss\Parser\Twitter;

use SocialRss\Data\PostData;
use SocialRss\Parser\Post\PostInterface;
use SocialRss\Parser\Twitter\Entity\EntityInterface;
use SocialRss\Parser\Twitter\Entity\HashtagEntity;
use SocialRss\Parser\Twitter\Entity\MediaPhotoEntity;
use SocialRss\Parser\Twitter\Entity\MediaVideoEntity;
use SocialRss\Parser\Twitter\Entity\SymbolEntity;
use SocialRss\Parser\Twitter\Entity\UnknownEntity;
use SocialRss\Parser\Twitter\Entity\UrlEntity;
use SocialRss\Parser\Twitter\Entity\UserMentionEntity;

class TwitterPost implements PostInterface
{
    /** @var array */
    protected $tweet;

    /** @var array */
    protected $originalTweet;

    public function __construct(array $item)
    {
        $this->tweet = $item['retweeted_status'] ?? $item;
        $this->originalTweet = $item;
    }

    public function getTitle(): string
    {
        $title = $this->getAuthorName();
        if ($this->isRetweetedStatus()) {
            $title .= " (RT by {$this->getOriginalAuthorName()})";
        }

        return $title;
    }

    public function getLink(): string
    {
        return TwitterParser::getUrl() . "{$this->tweet['user']['screen_name']}/status/{$this->tweet['id_str']}";
    }

    public function getContent(): string
    {
        $flatEntities = $this->getEntities();
        $entitiesMap = $this->getEntitiesMap();

        $processedText = $this->tweet['full_text'];
        foreach ($flatEntities as $entity) {
            foreach ($entitiesMap as $entityItem) {
                if ($entityItem::isApplicable($entity)) {
                    /** @var EntityInterface $entityParser */
                    $entityParser = new $entityItem($entity, $processedText);

                    $processedText = $entityParser->getParsedContent();
                }
            }
        }

        return nl2br(trim($processedText));
    }

    public function getDate(): \DateTime
    {
        return \DateTime::createFromFormat('D M j H:i:s P Y', $this->originalTweet['created_at']);
    }

    public function getTags(): array
    {
        if (!isset($this->tweet['entities']['hashtags'])) {
            return [];
        }

        return array_map(function ($hashtag) {
            return $hashtag['text'];
        }, $this->tweet['entities']['hashtags']);
    }

    public function getAuthorName(): string
    {
        return $this->tweet['user']['name'];
    }

    protected function getOriginalAuthorName(): string
    {
        return $this->originalTweet['user']['name'];
    }

    public function getAuthorAvatar(): string
    {
        return $this->tweet['user']['profile_image_url_https'];
    }

    public function getAuthorLink(): string
    {
        return TwitterParser::getUrl() . $this->tweet['user']['screen_name'];
    }

    public function getQuote(): ?PostData
    {
        if (!$this->hasQuote()) {
            return null;
        }

        $post = new self($this->tweet['quoted_status']);

        return new PostData([
            'title' => $post->getTitle(),
            'link' => $post->getLink(),
            'content' => $post->getContent(),
        ]);
    }

    protected function getEntities(): array
    {
        $tweetEntities = array_merge(
            $this->tweet['entities'],
            $this->tweet['extended_entities'] ?? []
        );

        $processedEntities = array_map(function ($type, $typeArray) {
            return array_map(function ($entity) use ($type) {
                $entity['entity_type'] = isset($entity['type']) ? "{$type}_{$entity['type']}" : $type;

                return $entity;
            }, $typeArray);
        }, array_keys($tweetEntities), $tweetEntities);

        return array_merge(...$processedEntities);
    }

    protected function getEntitiesMap(): array
    {
        return [
            HashtagEntity::class,
            UserMentionEntity::class,
            UrlEntity::class,
            SymbolEntity::class,
            MediaPhotoEntity::class,
            MediaVideoEntity::class,
            UnknownEntity::class,
        ];
    }

    protected function hasQuote(): bool
    {
        return isset($this->tweet['quoted_status']);
    }

    protected function isRetweetedStatus(): bool
    {
        return isset($this->originalTweet['retweeted_status']);
    }
}
