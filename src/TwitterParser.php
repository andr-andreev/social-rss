<?php
namespace SocialRSS;

class TwitterParser extends Parser
{
    const NAME = 'Twitter';
    const URL = 'https://twitter.com/';

    const API_URL = 'https://api.twitter.com/1.1/statuses/home_timeline.json';
    const API_PARAMETERS = '?count=100';

    public function __construct($feed, $config)
    {
        parent::__construct($feed);

        try {
            $twitter = new \TwitterAPIExchange($config);
            $twitterJson = $twitter->setGetfield(self::API_PARAMETERS)->buildOauth(self::API_URL, 'GET')->performRequest();
            $this->socialFeed = json_decode($twitterJson, true);

            if (isset($this->socialFeed['errors'])) {
                throw new \Exception($this->socialFeed['errors'][0]['message']);
            }
        } catch (\Exception $error) {
            exit($error->getMessage());
        }
    }

    protected function generateItem($item)
    {
        if (isset($item['retweeted_status'])) {
            $tweet = $item['retweeted_status'];
            $retweetPart = " (RT by {$item['user']['name']})";
        } else {
            $tweet = $item;
            $retweetPart = "";
        }

        $feedItem = new Item();
        $feedItem->setTitle($tweet['user']['name'] . $retweetPart);
        $feedItem->setLink(self::URL . "{$tweet['user']['screen_name']}/status/{$tweet['id_str']}");

        $avatar = $this->makeImg($tweet['user']['profile_image_url_https'], self::URL . $tweet['user']['screen_name']);
        $content = $this->parseContent($tweet);
        $feedItem->setDescription($this->makeBlock($avatar, $content));

        $feedItem->setAuthor($tweet['user']['name']);
        $feedItem->setDate(strtotime($item['created_at']));

        return $feedItem;
    }

    private function parseContent($tweet)
    {
        $map = [
            'hashtags' => function ($text, $item) {
                return $this->replaceContent(
                    $text,
                    "#{$item['text']}",
                    $this->makeLink(self::URL . "hashtag/{$item['text']}", "#{$item['text']}"));
            },
            'user_mentions' => function ($text, $item) {
                return $this->replaceContent(
                    $text,
                    "@{$item['screen_name']}",
                    $this->makeLink(self::URL . $item['screen_name'], "@{$item['screen_name']}"));
            },
            'urls' => function ($text, $item) {
                return $this->replaceContent(
                    $text,
                    $item['url'],
                    $this->makeLink($item['expanded_url'], $item['display_url']));
            },
            'media' => function ($text, $item) {
                return $this->replaceContent(
                    $text,
                    $item['url'],
                    '') .
                PHP_EOL . $this->makeImg($item['media_url_https'], $item['expanded_url']);
            }
        ];

        $text = $tweet['text'];

        foreach ($tweet['entities'] as $type => $items) {
            foreach ($items as $item) {
                $text = $map[$type]($text, $item);
            }
        }

        return nl2br(trim($text));
    }

    private function replaceContent($text, $search, $replace)
    {
        $quotedSearch = preg_quote($search, '/');
        return preg_replace("/({$quotedSearch})(?=[^>]*(<|$))/", $replace, $text, 1);
    }
}