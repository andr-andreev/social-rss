<?php
declare(strict_types = 1);

namespace SocialRss\Parser\Vk;

use SocialRss\Data\PostData;
use SocialRss\Parser\AbstractParser;
use SocialRss\Parser\Feed\FeedInterface;

class VkParser extends AbstractParser
{
    /** @var VkClient */
    protected $vkClient;

    public function __construct(array $config)
    {
        $this->vkClient = new VkClient($config);
    }

    /**
     * @throws \SocialRss\Exception\SocialRssException
     * @throws \VK\Exceptions\VKClientException
     */
    public function getFeed(string $username): array
    {
        return $this->vkClient->getFeed($username);
    }

    public static function getName(): string
    {
        return 'VK';
    }

    public static function getUrl(): string
    {
        return 'https://vk.com/';
    }

    public function getFeedParser(array $feed): FeedInterface
    {
        return new VkFeed($feed);
    }

    public function parsePost(array $item): PostData
    {
        return PostData::fromResponse(new VkPost($item));
    }
}
