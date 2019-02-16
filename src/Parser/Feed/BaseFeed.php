<?php
declare(strict_types=1);


namespace SocialRss\Parser\Feed;

abstract class BaseFeed implements FeedInterface
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
