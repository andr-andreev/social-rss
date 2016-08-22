<?php
namespace SocialRss\Parser\Twitter;

use SocialRss\Exception\SocialRssException;
use \TwitterAPIExchange;
use SocialRss\Parser\ParserInterface;
use SocialRss\Parser\ParserTrait;

/**
 * Class TwitterParser
 * @package SocialRss\Parser\Twitter
 */
class TwitterParser implements ParserInterface
{
    use ParserTrait;

    const NAME = 'Twitter';
    const URL = 'https://twitter.com/';

    const API_URL = 'https://api.twitter.com/1.1/statuses/home_timeline.json';
    const API_PARAMETERS = '?count=100';

    private $twitterClient;

    /**
     * TwitterParser constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->twitterClient = new TwitterAPIExchange($config);
    }

    /**
     * @return mixed
     * @throws SocialRssException
     */
    public function getFeed()
    {
        $twitterJson = $this->twitterClient
            ->setGetfield(self::API_PARAMETERS)
            ->buildOauth(self::API_URL, 'GET')
            ->performRequest();

        $feed = json_decode($twitterJson, true);

        if (isset($feed['errors'])) {
            throw new SocialRssException($feed['errors'][0]['message']);
        }

        return $feed;
    }

    /**
     * @param $feed
     * @return array
     */
    public function parseFeed($feed)
    {

        // Parse items
        $items = array_reduce($feed, function ($items, $item) {
            $itemParsed = $this->parseItem($item);

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
     * @return array
     */
    protected function parseItem($item)
    {
        $tweet = $item;
        $titlePart = "";

        if (isset($item['retweeted_status'])) {
            $tweet = $item['retweeted_status'];
            $titlePart = " (RT by {$item['user']['name']})";
        }

        $parsed = $this->parseContent($tweet);

        $quote = isset($tweet['quoted_status']) ? [
            'title' => $tweet['quoted_status']['user']['name'],
            'link' => self::URL .
                "{$tweet['quoted_status']['user']['screen_name']}/status/{$tweet['quoted_status']['id_str']}",
            'content' => $this->parseContent($tweet['quoted_status'])['content'],
        ] : [];

        return [
            'title' => $tweet['user']['name'] . $titlePart,
            'link' => self::URL . "{$tweet['user']['screen_name']}/status/{$tweet['id_str']}",
            'content' => $parsed['content'],
            'date' => strtotime($item['created_at']),
            'tags' => $parsed['tags'],
            'author' => [
                'name' => $tweet['user']['name'],
                'avatar' => $tweet['user']['profile_image_url_https'],
                'link' => self::URL . $tweet['user']['screen_name'],
            ],
            'quote' => $quote,
        ];
    }

    /**
     * @param $tweet
     * @return array
     */
    private function parseContent($tweet)
    {
        $tags = [];

        $map = [
            'hashtags' => function ($text, $item) use (&$tags) {
                $tags[] = $item['text'];

                return $this->replaceContent(
                    $text,
                    "#{$item['text']}",
                    $this->makeLink(self::URL . "hashtag/{$item['text']}", "#{$item['text']}")
                );
            },
            'user_mentions' => function ($text, $item) {
                return $this->replaceContent(
                    $text,
                    "@{$item['screen_name']}",
                    $this->makeLink(self::URL . $item['screen_name'], "@{$item['screen_name']}")
                );
            },
            'urls' => function ($text, $item) {
                return $this->replaceContent(
                    $text,
                    $item['url'],
                    $this->makeLink($item['expanded_url'], $item['display_url'])
                );
            },
            'media' => function ($text, $item) {
                return $this->replaceContent(
                    $text,
                    $item['url'],
                    ''
                ) .
                PHP_EOL . $this->makeImg($item['media_url_https'], $item['expanded_url']);
            },
            'symbols' => function ($text, $item) {
                return $this->replaceContent(
                    $text,
                    '$' . $item['text'],
                    $this->makeLink(self::URL . "search?q=%24{$item['text']}", '$' . $item['text'])
                );
            }
        ];

        $text = $tweet['text'];

        foreach ($tweet['entities'] as $type => $items) {
            foreach ($items as $item) {
                $text = isset($map[$type]) ? $map[$type]($text,
                    $item) : $text . PHP_EOL . "[Tweet contains unknown entity type $type]";
            }
        }

        return [
            'content' => nl2br(trim($text)),
            'tags' => $tags,
        ];
    }

    /**
     * @param $text
     * @param $search
     * @param $replace
     * @return mixed
     */
    private function replaceContent($text, $search, $replace)
    {
        $quotedSearch = preg_quote($search, '/');
        return preg_replace("/({$quotedSearch})(?=[^>]*(<|$))/", $replace, $text, 1);
    }
}
