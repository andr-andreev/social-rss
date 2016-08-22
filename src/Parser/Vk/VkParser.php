<?php

namespace SocialRss\Parser\Vk;

use SocialRss\Exception\SocialRssException;
use SocialRss\Parser\ParserInterface;
use VK\VK;
use SocialRss\Parser\ParserTrait;

/**
 * Class VkParser
 * @package SocialRss\Parser\Vk
 */
class VkParser implements ParserInterface
{
    use ParserTrait;

    const NAME = 'VK';
    const URL = 'https://vk.com/';

    const API_METHOD = 'newsfeed.get';
    const API_PARAMETERS = ['count' => 100];


    private $vkClient;

    /**
     * VkParser constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->vkClient = new VK($config['app_id'], $config['api_secret'], $config['access_token']);
    }

    /**
     * @return mixed
     * @throws SocialRssException
     */
    public function getFeed()
    {
        try {
            $socialFeed = $this->vkClient->api(self::API_METHOD, self::API_PARAMETERS);

            if (isset($socialFeed['error'])) {
                throw new SocialRssException($socialFeed['error']['error_msg']);
            }
        } catch (\Exception $error) {
            throw new SocialRssException($error->getMessage());
        }

        return $socialFeed['response'];
    }

    /**
     * @param $feed
     * @return array
     */
    public function parseFeed($feed)
    {
        // Get users array
        $users = array_reduce($feed['profiles'], function ($users, $user) {
            $uid = $user['uid'];
            $users[$uid] = $user;
            $users[$uid]['id'] = $uid;
            $users[$uid]['name'] = "{$users[$uid]['first_name']} {$users[$uid]['last_name']}";

            return $users;
        }, []);

        // Add groups to users array
        $users = array_reduce($feed['groups'], function ($users, $group) {
            $gid = -$group['gid'];
            $users[$gid] = $group;
            $users[$gid]['id'] = $gid;

            return $users;
        }, $users);

        // Parse items
        $items = array_reduce($feed['items'], function ($items, $item) use ($users) {
            $itemParsed = $this->parseItem($item, $users);

            if (empty($itemParsed)) {
                return $items;
            }

            $items[] = $itemParsed;

            return $items;
        }, []);

        return [
            'title' => self::NAME,
            'link' => self::URL,
            'items' => $items,
        ];
    }

    /**
     * @param $item
     * @param $users
     * @return array
     */
    protected function parseItem($item, $users)
    {
        $postParser = new PostParser($item, $users);

        $attachmentParser = new AttachmentParser($item);

        if (!$postParser->isParserTypeAvailable()) {
            return [];
        }

        $title = $postParser->parser->getTitle();
        $link = $postParser->parser->getLink();
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
            'title' => $title,
            'link' => $link,
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
