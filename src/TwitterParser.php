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
        $content = isset($tweet['quoted_status']) ? $this->parseContent($tweet) . $this->parseQuote($tweet['quoted_status']) : $this->parseContent($tweet);
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
            },
            'symbols' => function ($text, $item) {
                return $this->replaceContent(
                    $text,
                    '$' . $item['text'],
                    $this->makeLink(self::URL . "search?q=%24{$item['text']}", '$' . $item['text']));
            }
        ];

        $text = $tweet['text'];

        foreach ($tweet['entities'] as $type => $items) {
            foreach ($items as $item) {
                $text = isset($map[$type]) ? $map[$type]($text, $item) : $text . PHP_EOL . "[Tweet contains unknown entity type $type]";
            }
        }

        return nl2br(trim($text));
    }

    private function replaceContent($text, $search, $replace)
    {
        $quotedSearch = preg_quote($search, '/');
        return preg_replace("/({$quotedSearch})(?=[^>]*(<|$))/", $replace, $text, 1);
    }

    private function parseQuote($quote)
    {
        $avatar = $this->makeLink(self::URL . "{$quote['user']['screen_name']}/status/{$quote['id_str']}", "{$quote['user']['name']}");
        $content = $this->parseContent($quote);
        $quotedBlock = "<blockquote>" . $avatar . '<br>' . $content . "</blockquote>";

        return $quotedBlock;
    }
}