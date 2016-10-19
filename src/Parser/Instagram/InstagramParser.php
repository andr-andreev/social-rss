<?php

namespace SocialRss\Parser\Instagram;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Client;
use SocialRss\Parser\ParserInterface;
use SocialRss\Parser\ParserTrait;
use SocialRss\Exception\SocialRssException;

/**
 * Class InstagramParser
 * @package SocialRss\Parser\Instagram
 */
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

    /**
     * InstagramParser constructor.
     * @param $config
     */
    public function __construct($config)
    {

        $this->cookies = new CookieJar;
        $this->httpClient = new Client(['base_uri' => self::URL, 'cookies' => $this->cookies]);

        $this->config = $config;
    }

    /**
     * @param $username
     * @return mixed
     * @throws SocialRssException
     */
    public function getFeed($username)
    {
        // Due to new Instagram API update there is no ability to get users feed via the API
        // (deprecation of /users/self/feed endpoint).
        // https://www.instagram.com/developer/changelog/

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

        $url = empty($username) ? '/' : '/' . $username;
        // Open URL with data as a logged in user
        $feedRequest = $this->httpClient->request('GET', $url, [
            'headers' => self::HEADERS
        ]);

        // Find JSON data in the script tag
        $reResult = preg_match(
            "/<script.*>window\\._sharedData = (.*?);<\\/script>/",
            $feedRequest->getBody(),
            $matches
        );
        if ($reResult === 0) {
            throw new SocialRssException('Failed to find data');
        }
        $instagramJson = $matches[1];

        $feed = json_decode($instagramJson, true);

        return empty($username) ?
            $feed['entry_data']['FeedPage'][0]['feed']['media']['nodes'] :
            $this->processFeed($feed);
    }

    /**
     * @param $feed
     * @return array
     */
    private function processFeed($feed)
    {
        $user = $feed['entry_data']['ProfilePage'][0]['user'];

        return array_map(function ($item) use ($user) {
            $item['owner'] = $user;
            return $item;
        }, $feed['entry_data']['ProfilePage'][0]['user']['media']['nodes']);
    }


    /**
     * @param $feed
     * @return array
     */
    public function parseFeed($feed)
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

    /**
     * @param $item
     * @return string
     */
    private function parseContent($item)
    {
        $location = isset($item['location']['name']) ? $item['location']['name'] : '';

        // Use image or video
        $media = ($item['is_video'] && isset($item['video_url'])) ? $this->makeVideo(
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

    /**
     * @param $url
     * @return string
     */
    private function cleanUrl($url)
    {
        // Remove unnecessary query string
        // https://scontent-[...].cdninstagram.com/[...].jpg?ig_cache_key=MTIzNDUxMjM0NTEyMzQ1
        return strtok($url, '?');
    }
}
