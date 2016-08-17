<?php
namespace SocialRss\Parser\Twitter;

use SocialRss\Exception\SocialRssException;
use \TwitterAPIExchange;
use SocialRss\Parser\ParserInterface;
use SocialRss\Parser\ParserTrait;

class TwitterParser implements ParserInterface
{
    use ParserTrait;

    const NAME = 'Twitter';
    const URL = 'https://twitter.com/';

    const API_URL = 'https://api.twitter.com/1.1/statuses/home_timeline.json';
    const API_PARAMETERS = '?count=100';

    private $twitterClient;

    public function __construct($config)
    {
        $this->twitterClient = new TwitterAPIExchange($config);
    }

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

    public function parseFeed($feed)
    {
        $items = array_reduce($feed, function ($carry, $item) {
            $parsedItem = $this->parseItem($item);

            if (!is_null($parsedItem)) {
                $carry[] = $parsedItem;
            }

            return $carry;
        }, []);

        return [
            'title' => self::NAME,
            'link' => self::URL,
            'items' => $items,
        ];
    }

    protected function parseItem($item)
    {
        if (isset($item['retweeted_status'])) {
            $tweet = $item['retweeted_status'];
            $retweetPart = " (RT by {$item['user']['name']})";
        } else {
            $tweet = $item;
            $retweetPart = "";
        }

        $parsed = $this->parseContent($tweet);

        $quote = isset($tweet['quoted_status']) ? [
            'title' => $tweet['quoted_status']['user']['name'],
            'link' => self::URL .
                "{$tweet['quoted_status']['user']['screen_name']}/status/{$tweet['quoted_status']['id_str']}",
            'content' => $this->parseContent($tweet['quoted_status'])['content'],
        ] : [];

        return [
            'title' => $tweet['user']['name'] . $retweetPart,
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

    private function replaceContent($text, $search, $replace)
    {
        $quotedSearch = preg_quote($search, '/');
        return preg_replace("/({$quotedSearch})(?=[^>]*(<|$))/", $replace, $text, 1);
    }
}
