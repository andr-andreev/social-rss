<?php
declare(strict_types = 1);

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
    public function __construct(array $config)
    {
        $this->cookies = new CookieJar;
        $this->httpClient = new Client(['base_uri' => self::URL, 'cookies' => $this->cookies]);

        $this->config = $config;
    }

    /**
     * @param $username
     * @return array
     * @throws SocialRssException
     */
    public function getFeed(string $username): array
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
            (string)$feedRequest->getBody(),
            $matches
        );
        if ($reResult === 0) {
            throw new SocialRssException('Failed to find data');
        }
        $instagramJson = $matches[1];

        $feed = json_decode($instagramJson, true);

        return empty($username) ? $this->processFeedPage($feed) : $this->processProfilePage($feed);
    }

    /**
     * @param $items
     * @return array
     */
    private function processFeed(array $items): array
    {
        return array_map(function ($item) {
            $item['caption'] = $item['caption'] ?? '';
            $item['location']['name'] = $item['location']['name'] ?? '';

            return $item;
        }, $items);
    }

    /**
     * @param $feed
     * @return array
     */
    private function processFeedPage(array $feed): array
    {
        return $this->processFeed($feed['entry_data']['FeedPage'][0]['feed']['media']['nodes']);
    }

    /**
     * @param $feed
     * @return array
     */
    private function processProfilePage(array $feed): array
    {
        $items = $this->processFeed($feed['entry_data']['ProfilePage'][0]['user']['media']['nodes']);

        $user = $feed['entry_data']['ProfilePage'][0]['user'];

        return array_map(function ($item) use ($user) {
            $item['owner'] = $user;

            return $item;
        }, $items);
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
    private function parseItem(array $item): array
    {
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
    private function parseContent(array $item): string
    {
        $location = $item['location']['name'];

        // Use image or video
        $media = ($item['is_video'] && isset($item['video_url'])) ? $this->makeVideo(
            $item['video_url'],
            $item['display_src']
        ) : $this->makeImg($this->cleanUrl($item['display_src']));

        // Match #hashtags
        $caption = $this->parseByPattern(
            '#',
            '<a href="https://www.instagram.com/explore/tags/{{string}}/">#{{string}}</a>',
            $item['caption']
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
    private function cleanUrl($url): string
    {
        // Remove unnecessary query string
        // https://scontent-[...].cdninstagram.com/[...].jpg?ig_cache_key=MTIzNDUxMjM0NTEyMzQ1
        return strtok($url, '?');
    }
}
