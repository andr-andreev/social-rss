<?php
declare(strict_types = 1);

namespace SocialRss\Parser\Vk;

use SocialRss\Parser\AbstractParser;
use SocialRss\Parser\Feed\FeedInterface;
use SocialRss\Parser\FeedItem\FeedItemInterface;

/**
 * Class VkParser
 *
 * @package SocialRss\Parser\Vk
 */
class VkParser extends AbstractParser
{
    protected $vkClient;

    /**
     * VkParser constructor.
     *
     * @param $config
     */
    public function __construct(array $config)
    {
        $this->vkClient = new VkClient($config);
    }

    /**
     * @param string $username
     * @return array
     * @throws \SocialRss\Exception\SocialRssException
     * @throws \VK\Exceptions\VKClientException
     */
    public function getFeed(string $username): array
    {
        return $this->vkClient->getFeed($username);
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return 'VK';
    }

    /**
     * @return string
     */
    public static function getUrl(): string
    {
        return 'https://vk.com/';
    }

    /**
     * @param array $feed
     * @return FeedInterface
     */
    public function getFeedParser(array $feed): FeedInterface
    {
        return new VkFeed($feed);
    }

    /**
     * @param array $item
     * @return FeedItemInterface
     */
    public function createFeedItemParser(array $item): FeedItemInterface
    {
        return new VkFeedItem($item);
    }
}
