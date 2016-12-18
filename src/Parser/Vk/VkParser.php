<?php
declare(strict_types = 1);

namespace SocialRss\Parser\Vk;

use SocialRss\Exception\SocialRssException;
use SocialRss\Parser\ParserInterface;
use VK\VK;
use SocialRss\Parser\ParserTrait;

/**
 * Class VkParser
 *
 * @package SocialRss\Parser\Vk
 */
class VkParser implements ParserInterface
{
    use ParserTrait;

    const NAME = 'VK';
    const URL = 'https://vk.com/';

    const API_METHOD_HOME = 'newsfeed.get';
    const API_METHOD_USER = 'wall.get';
    const API_PARAMETERS = ['count' => 100, 'extended' => 1];

    const CONFIG_DEFAULT = [
        'app_id' => '',
        'api_secret' => '',
        'access_token' => '',
    ];

    private $vkClient;

    /**
     * VkParser constructor.
     *
     * @param $config
     */
    public function __construct(array $config)
    {
        $vkConfig = array_merge(self::CONFIG_DEFAULT, $config);
        $this->vkClient = new VK($vkConfig['app_id'], $vkConfig['api_secret'], $vkConfig['access_token']);
    }

    /**
     * @param $username
     * @return array
     * @throws SocialRssException
     */
    public function getFeed(string $username): array
    {
        if (empty($username)) {
            $method = self::API_METHOD_HOME;
            $parameters = self::API_PARAMETERS;
        } else {
            $method = self::API_METHOD_USER;
            $parameters = array_merge(self::API_PARAMETERS, ['domain' => $username]);
        }
        $socialFeed = $this->vkClient->api($method, $parameters);

        if (isset($socialFeed['error'])) {
            throw new SocialRssException($socialFeed['error']['error_msg']);
        }

        return empty($username) ? $socialFeed['response'] : $this->processFeed($socialFeed['response']);
    }

    /**
     * @param $feed
     * @return array
     */
    private function processFeed(array $feed): array
    {
        $items = array_filter($feed['wall'], function ($item) {
            return is_array($item);
        });

        $processedItems = array_map(function ($item) {
            $item['type'] = $item['post_type'];
            $item['source_id'] = $item['from_id'];
            $item['post_id'] = $item['id'];

            return $item;
        }, $items);

        return array_merge($feed, ['items' => $processedItems]);
    }

    /**
     * @param $feed
     * @return array
     */
    public function parseFeed(array $feed): array
    {
        // Get groups array
        $groups = array_reduce($feed['groups'], function ($groups, $group) {
            $gid = -$group['gid'];
            $groups[$gid] = $group;
            $groups[$gid]['id'] = $gid;

            return $groups;
        }, []);

        // Get combined groups and users array
        $profiles = array_reduce($feed['profiles'], function ($users, $user) {
            $uid = $user['uid'];
            $users[$uid] = $user;
            $users[$uid]['id'] = $uid;
            $users[$uid]['name'] = "{$users[$uid]['first_name']} {$users[$uid]['last_name']}";

            return $users;
        }, $groups);

        // Parse items
        $items = array_map(function ($item) use ($profiles) {
            return $this->parseItem($item, $profiles);
        }, $feed['items']);

        $filtered = array_filter($items, function ($item) {
            return !empty($item);
        });

        return [
            'title' => self::NAME,
            'link' => self::URL,
            'items' => $filtered,
        ];
    }

    /**
     * @param $item
     * @param $users
     * @return array
     */
    protected function parseItem(array $item, array $users): array
    {
        $postParser = new PostParser($item, $users);

        $attachmentParser = new AttachmentParser($item);

        if (!$postParser->isParserAvailable()) {
            return [];
        }

        $content = $postParser->parser->getDescription();
        $quote = $postParser->parser->getQuote();

        $attachments = $attachmentParser->parseAttachments();

        if (!empty($quote)) {
            // Swap user and repost contents
            $tmp = $quote['content'];
            $quote['content'] = $content;
            $content = $tmp;
        }

        $contentAddition = '';
        $quoteContentAddition = '';

        if (!empty($quote)) {
            $quoteContentAddition = $attachments;
        } else {
            $contentAddition = $attachments;
        }

        $content .= PHP_EOL . $contentAddition;
        $content = nl2br(trim($content));

        if (isset($quote['content'])) {
            $quote['content'] .= PHP_EOL . $quoteContentAddition;
            $quote['content'] = nl2br(trim($quote['content']));
        }

        return [
            'title' => $postParser->parser->getTitle(),
            'link' => $postParser->parser->getLink(),
            'content' => $content,
            'date' => $item['date'],
            'tags' => $this->getParsedByPattern('#{string}', $content),
            'author' => [
                'name' => $users[$item['source_id']]['name'],
                'avatar' => $users[$item['source_id']]['photo'],
                'link' => self::URL . $users[$item['source_id']]['screen_name'],
            ],
            'quote' => $quote,
        ];
    }
}
