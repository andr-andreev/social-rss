<?php
declare(strict_types=1);


namespace SocialRss\Parser\Feed;

/**
 * Class BaseFeed
 * @package SocialRss\Parser\Feed
 */
class BaseFeed implements FeedInterface
{
    /** @var array */
    protected $feed;

    public function __construct(array $feed)
    {
        $this->feed = $feed;
    }

    public function getItems(): array
    {
        return $this->feed;
    }
}
