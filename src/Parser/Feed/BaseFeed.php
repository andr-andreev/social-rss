<?php
declare(strict_types=1);


namespace SocialRss\Parser\Feed;

/**
 * Class BaseFeed
 * @package SocialRss\Parser\Feed
 */
class BaseFeed implements FeedInterface
{
    protected $feed;

    /**
     * BaseFeed constructor.
     * @param array $feed
     */
    public function __construct(array $feed)
    {
        $this->feed = $feed;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->feed;
    }
}
