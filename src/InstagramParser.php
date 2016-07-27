<?php

namespace SocialRSS;

class InstagramParser extends Parser
{
    const NAME = 'Instagram';
    const URL = 'https://www.instagram.com/';

    public function __construct($feed, $config)
    {
        parent::__construct($feed);

        try {
            $headers = [
                'Referer' => self::URL,
                'User-Agent' => 'Mozilla/5.0',
            ];

            $jar = new \GuzzleHttp\Cookie\CookieJar;
            $client = new \GuzzleHttp\Client(['base_uri' => self::URL, 'cookies' => $jar]);

            // Open instagram homepage
            $homepageRequest = $client->request('GET', '/', [
                'headers' => $headers,
            ]);

            // Make cookies array
            $cookies = array_reduce($jar->toArray(), function ($carry, $item) {
                $carry[$item['Name']] = $item['Value'];

                return $carry;
            }, []);

            // Login and get sessionid cookie
            $loginRequest = $client->request('POST', '/accounts/login/ajax/', [
                'headers' => array_merge($headers, [
                    'X-CSRFToken' => $cookies['csrftoken'],
                ]),
                'form_params' => [
                    'username' => $config['username'],
                    'password' => $config['password']
                ]
            ]);

            $loginBody = json_decode((string)($loginRequest->getBody()), true);
            if ($loginBody['authenticated'] === false) {
                throw new \Exception('Failed to login');
            }

            // Open homepage as a logged in user
            $feedRequest = $client->request('GET', '/', [
                'headers' => $headers
            ]);

            // Find JSON data in the script tag
            preg_match("/<script.*>window\\._sharedData = (.*?);<\\/script>/", $feedRequest->getBody(), $matches);
            $instagramJson = $matches[1];
            $this->socialFeed = json_decode($instagramJson, true)['entry_data']['FeedPage'][0]['feed']['media']['nodes'];

        } catch (\Exception $error) {
            exit($error->getMessage());
        }

    }

    protected function generateItem($item)
    {
        $feedItem = new Item();
        $feedItem->setTitle($item['owner']['username']);
        $feedItem->setLink(self::URL . "p/{$item['code']}/");

        $avatar = $this->makeImg($item['owner']['profile_pic_url'], self::URL . $item['owner']['username'] . '/');
        $content = $this->parseContent($item);
        $feedItem->setDescription($this->makeBlock($avatar, $content));

        $feedItem->setAuthor($item['owner']['username']);
        $feedItem->setDate($item['date']);

        return $feedItem;
    }

    private function parseContent($item)
    {
        $caption = $item['caption'];

        // Match #hashtags
        $caption = preg_replace('/(^|)#(\w*[a-zA-Zа-яА-Я_]+\w*)/u', '\1<a href="https://www.instagram.com/explore/tags/\2/">#\2</a>', $caption);

        // Match @mentions
        $caption = preg_replace('/(^|)@(\w*[a-zA-Zа-яА-Я_]+\w*)/u', '\1<a href="https://www.instagram.com/\2/">@\2</a>', $caption);

        // Use image or video
        $media = $item['is_video'] ? $this->makeVideo($item['video_url'], $item['display_src']) : $this->makeImg(self::cleanUrl($item['display_src']));

        $content = $item['location']['name'] . PHP_EOL . $media . PHP_EOL . $caption;

        return nl2br(trim($content));
    }

    private function cleanUrl($url)
    {
        // Remove unnecessary query string https://scontent-[...].cdninstagram.com/[...].jpg?ig_cache_key=MTIzNDUxMjM0NTEyMzQ1
        return strtok($url, '?');
    }

}