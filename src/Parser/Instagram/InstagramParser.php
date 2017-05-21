<?php
declare(strict_types = 1);

namespace SocialRss\Parser\Instagram;

use SocialRss\Parser\AbstractParser;
use SocialRss\Parser\Feed\FeedInterface;
use SocialRss\Parser\FeedItem\FeedItemInterface;

/**
 * Class InstagramParser
 *
 * @package SocialRss\Parser\Instagram
 */
class InstagramParser extends AbstractParser
{
    protected $instagramClient;

    /**
     * InstagramParser constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->instagramClient = new InstagramClient($config);
    }

    /**
     * @param string $username
     * @return array
     * @throws \SocialRss\Exception\SocialRssException
     */
    public function getFeed(string $username): array
    {
        return $this->instagramClient->getFeed($username);
    }


    /**
     * @return string
     */
    public static function getName(): string
    {
        return 'Instagram';
    }

    /**
     * @return string
     */
    public static function getUrl(): string
    {
        return 'https://www.instagram.com/';
    }

    /**
     * @param array $feed
     * @return FeedInterface
     */
    public function getFeedParser(array $feed): FeedInterface
    {
        return new InstagramFeed($feed);
    }

    /**
     * @param array $item
     * @return FeedItemInterface
     */
    public function createFeedItemParser(array $item): FeedItemInterface
    {
        return new InstagramFeedItem($item);
    }
}
