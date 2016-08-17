<?php

namespace SocialRss\Parser\Instagram;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Client;
use SocialRss\Parser\ParserInterface;
use SocialRss\Parser\ParserTrait;
use SocialRss\Exception\SocialRssException;

class InstagramParser implements ParserInterface
{
    use ParserTrait;

    const NAME = 'Instagram';
    const URL = 'https://www.instagram.com/';

    const HEADERS = [
        'Referer' => self::URL,
        'User-Agent' => 'Mozilla/5.0',
    ];

    private $httpClient;
    private $cookies;
    private $config;

    public function __construct($config)
    {

        $this->cookies = new CookieJar;
        $this->httpClient = new Client(['base_uri' => self::URL, 'cookies' => $this->cookies]);

        $this->config = $config;
    }

    public function getFeed()
    {
        // Due to new Instagram API update there is no ability to get users feed via the API
        // (deprecation of /users/self/feed endpoint).
        // https://www.instagram.com/developer/changelog/

        try {
            // Open instagram homepage
            $this->httpClient->request('GET', '/', [
                'headers' => self::HEADERS,
            ]);

            // Make cookies array
            $cookies = array_reduce($this->cookies->toArray(), function ($carry, $item) {
                $carry[$item['Name']] = $item['Value'];

                return $carry;
            }, []);

            // Login and get sessionid cookie
            $loginRequest = $this->httpClient->request('POST', '/accounts/login/ajax/', [
                'headers' => array_merge(self::HEADERS, [
                    'X-CSRFToken' => $cookies['csrftoken'],
                ]),
                'form_params' => [
                    'username' => $this->config['username'],
                    'password' => $this->config['password']
                ]
            ]);

            $loginBody = json_decode((string)($loginRequest->getBody()), true);
            if ($loginBody['authenticated'] === false) {
                throw new SocialRssException('Failed to login');
            }

            // Open homepage as a logged in user
            $feedRequest = $this->httpClient->request('GET', '/', [
                'headers' => self::HEADERS
            ]);

            // Find JSON data in the script tag
            preg_match("/<script.*>window\\._sharedData = (.*?);<\\/script>/", $feedRequest->getBody(), $matches);
            $instagramJson = $matches[1];
        } catch (\Exception $error) {
            throw new SocialRssException($error->getMessage());
        }

        return json_decode($instagramJson, true)['entry_data']['FeedPage'][0]['feed']['media']['nodes'];
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

    private function parseItem($item)
    {
        if (!isset($item['caption'])) {
            $item['caption'] = '';
        }

        return [
            'title' => $item['owner']['username'],
            'link' => self::URL . "p/{$item['code']}/",
            'content' => $this->parseContent($item),
            'date' => $item['date'],
            'tags' => $this->getParsedByPattern('#{string}', $item['caption']),
            'author' => [
                'name' => $item['owner']['username'],
                'avatar' => $item['owner']['profile_pic_url'],
                'link' => self::URL . "{$item['owner']['username']}/",
            ]
        ];
    }

    private function parseContent($item)
    {
        $location = $item['location']['name'];

        // Use image or video
        $media = $item['is_video'] ? $this->makeVideo(
            $item['video_url'],
            $item['display_src']
        ) : $this->makeImg(self::cleanUrl($item['display_src']));

        $caption = $item['caption'];

        // Match #hashtags
        $caption = $this->parseByPattern(
            '#',
            '<a href="https://www.instagram.com/explore/tags/{{string}}/">#{{string}}</a>',
            $caption
        );

        // Match @mentions
        $caption = $this->parseByPattern(
            '@',
            '<a href="https://www.instagram.com/{{string}}/">@{{string}}</a>',
            $caption
        );

        $content = $location . PHP_EOL . $media . PHP_EOL . $caption;

        return nl2br(trim($content));
    }

    private function cleanUrl($url)
    {
        // Remove unnecessary query string
        // https://scontent-[...].cdninstagram.com/[...].jpg?ig_cache_key=MTIzNDUxMjM0NTEyMzQ1
        return strtok($url, '?');
    }
}
