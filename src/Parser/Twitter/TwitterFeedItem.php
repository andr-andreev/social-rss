<?php
declare(strict_types=1);


namespace SocialRss\Parser\Twitter;

use SocialRss\ParsedFeed\ParsedFeedItem;
use SocialRss\Parser\FeedItem\FeedItemInterface;
use SocialRss\Parser\Twitter\Entity\HashtagsEntity;
use SocialRss\Parser\Twitter\Entity\MediaAnimatedGifEntity;
use SocialRss\Parser\Twitter\Entity\MediaPhotoEntity;
use SocialRss\Parser\Twitter\Entity\MediaVideoEntity;
use SocialRss\Parser\Twitter\Entity\SymbolsEntity;
use SocialRss\Parser\Twitter\Entity\UnknownEntity;
use SocialRss\Parser\Twitter\Entity\UrlsEntity;
use SocialRss\Parser\Twitter\Entity\UserMentionsEntity;

/**
 * Class TwitterFeedItem
 * @package SocialRss\Parser\Twitter
 */
class TwitterFeedItem implements FeedItemInterface
{
    private $tweet;
    private $originalTweet;

    /**
     * TwitterFeedItem constructor.
     * @param array $item
     */
    public function __construct(array $item)
    {
        $tweet = $item['retweeted_status'] ?? $item;

        $this->tweet = $tweet;
        $this->originalTweet = $item;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        $title = $this->getAuthorName();
        if ($this->isRetweetedStatus()) {
            $title .= " (RT by {$this->getOriginalAuthorName()})";
        }

        return $title;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return TwitterParser::getUrl() . "{$this->tweet['user']['screen_name']}/status/{$this->tweet['id_str']}";
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        $tweetEntities = array_merge(
            $this->tweet['entities'],
            isset($this->tweet['extended_entities']) ? $this->tweet['extended_entities'] : []
        );

        $processedEntities = array_map(function ($type, $typeArray) {
            return array_map(function ($entity) use ($type) {
                $entity['entity_type'] = isset($entity['type']) ? "{$type}_{$entity['type']}" : $type;

                return $entity;
            }, $typeArray);
        }, array_keys($tweetEntities), $tweetEntities);

        $flatEntities = array_merge(...$processedEntities);

        $entitiesMap = [
            'hashtags' => HashtagsEntity::class,
            'user_mentions' => UserMentionsEntity::class,
            'urls' => UrlsEntity::class,
            'symbols' => SymbolsEntity::class,
            'media_photo' => MediaPhotoEntity::class,
            'media_video' => MediaVideoEntity::class,
            'media_animated_gif' => MediaAnimatedGifEntity::class,
        ];

        $processedText = array_reduce($flatEntities, function ($acc, $entity) use ($entitiesMap) {
            $type = $entity['entity_type'];
            $class = isset($entitiesMap[$type]) ? $entitiesMap[$type] : UnknownEntity::class;

            /** @var \SocialRss\Parser\Twitter\Entity\EntityInterface $entityParser */
            $entityParser = (new $class($entity, $acc));

            return $entityParser->getParsedContent();
        }, $this->tweet['full_text']);

        return nl2br(trim($processedText));
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return \DateTime::createFromFormat('D M j H:i:s P Y', $this->originalTweet['created_at']);
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        if (!isset($this->tweet['entities']['hashtags'])) {
            return [];
        }

        return array_map(function ($hashtag) {
            return $hashtag['text'];
        }, $this->tweet['entities']['hashtags']);
    }

    /**
     * @return string
     */
    public function getAuthorName(): string
    {
        return $this->tweet['user']['name'];
    }

    /**
     * @return string
     */
    private function getOriginalAuthorName(): string
    {
        return $this->originalTweet['user']['name'];
    }

    /**
     * @return mixed
     */
    public function getAuthorAvatar()
    {
        return $this->tweet['user']['profile_image_url_https'];
    }

    /**
     * @return string
     */
    public function getAuthorLink(): string
    {
        return TwitterParser::getUrl() . $this->tweet['user']['screen_name'];
    }

    /**
     * @return null|ParsedFeedItem
     */
    public function getQuote():?ParsedFeedItem
    {
        if (!$this->isQuotedStatus()) {
            return null;
        }

        $feedItem = new self($this->tweet['quoted_status']);

        return new ParsedFeedItem(
            $feedItem->getTitle(),
            $feedItem->getLink(),
            $feedItem->getContent()
        );
    }

    /**
     * @return bool
     */
    private function isQuotedStatus(): bool
    {
        return isset($this->tweet['quoted_status']);
    }

    /**
     * @return bool
     */
    private function isRetweetedStatus(): bool
    {
        return isset($this->originalTweet['retweeted_status']);
    }
}