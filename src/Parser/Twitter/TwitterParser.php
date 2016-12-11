<?php
declare(strict_types = 1);

namespace SocialRss\Parser\Twitter;

use SocialRss\Exception\SocialRssException;
use \TwitterAPIExchange;
use SocialRss\Parser\ParserInterface;
use SocialRss\Parser\ParserTrait;

/**
 * Class TwitterParser
 *
 * @package SocialRss\Parser\Twitter
 */
class TwitterParser implements ParserInterface
{
    use ParserTrait;

    const NAME = 'Twitter';
    const URL = 'https://twitter.com/';

    const API_URL_HOME = 'https://api.twitter.com/1.1/statuses/home_timeline.json';
    const API_URL_USER = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    const API_PARAMETERS = '?count=100&tweet_mode=extended';

    const CONFIG_DEFAULT = [
        'consumer_key' => '',
        'consumer_secret' => '',
        'oauth_access_token' => '',
        'oauth_access_token_secret' => '',
    ];

    private $twitterClient;

    /**
     * TwitterParser constructor.
     * @param $config
     */
    public function __construct(array $config)
    {
        $twitterConfig = array_merge(self::CONFIG_DEFAULT, $config);
        $this->twitterClient = new TwitterAPIExchange($twitterConfig);
    }

    /**
     * @param $username
     * @return mixed
     * @throws SocialRssException
     */
    public function getFeed(string $username): array
    {
        if (empty($username)) {
            $url = self::API_URL_HOME;
            $parameters = self::API_PARAMETERS;
        } else {
            $url = self::API_URL_USER;
            $parameters = self::API_PARAMETERS . "&screen_name={$username}";
        }

        $twitterJson = $this->twitterClient
            ->setGetfield($parameters)
            ->buildOauth($url, 'GET')
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
    public function parseFeed(array $feed): array
    {
        // Parse items
        $items = array_map(function ($item) {
            return $this->parseItem($item);
        }, $feed);

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
     * @return array
     */
    protected function parseItem(array $item)
    {
        $tweet = $item['retweeted_status'] ?? $item;

        $parsedStatus = $this->parseStatus($tweet);

        $parsedStatus['date'] = strtotime($item['created_at']);

        if (isset($item['retweeted_status'])) {
            $parsedStatus['title'] .= " (RT by {$item['user']['name']})";
        }

        if (isset($tweet['quoted_status'])) {
            $parsedStatus['quote'] = $this->parseStatus($tweet['quoted_status']);
        }

        return $parsedStatus;
    }

    /**
     * @param $tweet
     * @return array
     */
    private function parseStatus(array $tweet)
    {
        return [
            'title' => $tweet['user']['name'],
            'link' => self::URL . "{$tweet['user']['screen_name']}/status/{$tweet['id_str']}",
            'content' => $this->parseContent($tweet),
            'date' => strtotime($tweet['created_at']),
            'tags' => $this->parseTags($tweet),
            'author' => [
                'name' => $tweet['user']['name'],
                'avatar' => $tweet['user']['profile_image_url_https'],
                'link' => self::URL . $tweet['user']['screen_name'],
            ],
        ];
    }

    /**
     * @param array $tweet
     * @return string
     */
    private function parseContent(array $tweet): string
    {
        $tweetEntities = array_merge(
            $tweet['entities'],
            isset($tweet['extended_entities']) ? $tweet['extended_entities'] : []
        );

        $processedEntities = array_map(function ($type, $typeArray) {
            return array_map(function ($entity) use ($type) {
                $entity['entity_type'] = $type;
                return $entity;
            }, $typeArray);
        }, array_keys($tweetEntities), $tweetEntities);

        $flatEntities = array_merge(...$processedEntities);

        $entitiesMap = $this->getEntitiesMap();

        $processedText = array_reduce(
            $flatEntities,
            function ($acc, $entity) use ($entitiesMap) {
                $type = $entity['entity_type'];
                if (!isset($entitiesMap[$type])) {
                    return $acc . PHP_EOL .
                        "[Tweet contains unknown entity type {$entity['type']}]";
                }
                return $entitiesMap[$type]($acc, $entity);
            },
            $tweet['full_text']
        );

        return nl2br(trim($processedText));
    }

    /**
     * @param $tweet
     * @return array
     */
    private function parseTags(array $tweet): array
    {
        if (!isset($tweet['entities']['hashtags'])) {
            return [];
        }

        return array_map(function ($hashtag) {
            return $hashtag['text'];
        }, $tweet['entities']['hashtags']);
    }

    /**
     * @return array
     */
    private function getEntitiesMap(): array
    {
        return [
            'hashtags' => function ($text, $item) {
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
            'symbols' => function ($text, $item) {
                return $this->replaceContent(
                    $text,
                    '$' . $item['text'],
                    $this->makeLink(self::URL . "search?q=%24{$item['text']}", '$' . $item['text'])
                );
            },
            'media' => function ($text, $item) {
                switch ($item['type']) {
                    case 'photo':
                        return $this->replaceContent($text, $item['url'], '') .
                            PHP_EOL .
                            $this->makeImg($item['media_url_https'], $item['expanded_url']);

                    case 'video':
                    case 'animated_gif':
                        $videoVariants = array_filter($item['video_info']['variants'], function ($variant) {
                            return $variant['content_type'] === 'video/mp4';
                        });

                        if (empty($videoVariants)) {
                            $media = $this->makeImg($item['media_url_https']);
                        } else {
                            // first element in $videoVariants array
                            $media = $this->makeVideo(reset($videoVariants)['url'], $item['media_url_https']);
                        }

                        return $this->replaceContent($text, $item['url'], '') . PHP_EOL . $media;

                    default:
                        return $text . PHP_EOL . "[Tweet contains unknown media type {$item['type']}]";
                }
            },
        ];
    }

    /**
     * @param $text
     * @param $search
     * @param $replace
     * @return string
     */
    private function replaceContent(string $text, string $search, string $replace): string
    {
        $quotedSearch = preg_quote($search, '/');
        // replace text except already replaced inside HTML tags
        return preg_replace("/({$quotedSearch})(?=[^>]*(<|$))/i", $replace, $text, 1);
    }
}
