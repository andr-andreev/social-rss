<?php
declare(strict_types=1);

namespace SocialRss\Parser\Twitter;

use SocialRss\Data\PostData;
use SocialRss\Parser\AbstractParser;
use SocialRss\Parser\Feed\FeedInterface;

class TwitterParser extends AbstractParser
{
    protected $twitterClient;

    /**
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function __construct(array $config)
    {
        $this->twitterClient = new TwitterClient($config);
    }

    /**
     * @throws \SocialRss\Exception\SocialRssException
     */
    public function getFeed(string $username): array
    {
        return $this->twitterClient->getFeed($username);
    }

    public static function getName(): string
    {
        return 'Twitter';
    }

    public static function getUrl(): string
    {
        return 'https://twitter.com/';
    }


    public function parsePost(array $item): PostData
    {
        return PostData::fromResponse(new TwitterPost($item));
    }
}
