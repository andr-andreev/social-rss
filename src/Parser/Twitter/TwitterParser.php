<?php
declare(strict_types=1);

namespace SocialRss\Parser\Twitter;

use SocialRss\Parser\AbstractParser;
use SocialRss\Parser\FeedItem\FeedItemInterface;
use SocialRss\Parser\ParserInterface;

/**
 * Class TwitterParser
 *
 * @package SocialRss\Parser\Twitter
 */
class TwitterParser extends AbstractParser implements ParserInterface
{
    private $twitterClient;

    /**
     * TwitterParser constructor.
     *
     * @param $config
     */
    public function __construct(array $config)
    {
        $this->twitterClient = new TwitterClient($config);
    }

    /**
     * @param string $username
     * @return array
     */
    public function getFeed(string $username): array
    {
        return $this->twitterClient->getFeed($username);
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return 'Twitter';
    }

    /**
     * @return string
     */
    public static function getUrl(): string
    {
        return 'https://twitter.com/';
    }


    /**
     * @param array $item
     * @return FeedItemInterface
     */
    public function createFeedItemParser(array $item): FeedItemInterface
    {
        return new TwitterFeedItem($item);
    }
}
