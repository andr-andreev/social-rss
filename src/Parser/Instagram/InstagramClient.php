<?php
declare(strict_types=1);


namespace SocialRss\Parser\Instagram;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use SocialRss\Exception\SocialRssException;
use SocialRss\Parser\Client\ClientInterface;

/**
 * Class InstagramClient
 * @package SocialRss\Parser\Instagram
 */
class InstagramClient implements ClientInterface
{
    protected $httpClient;
    protected $httpHeaders;

    protected $cookies;
    protected $config;

    /**
     * InstagramClient constructor.
     *
     * @param $config
     */
    public function __construct(array $config)
    {
        $this->cookies = new CookieJar;
        $this->httpClient = new Client(['base_uri' => InstagramParser::getUrl(), 'cookies' => $this->cookies]);
        $this->httpHeaders = [
            'Referer' => InstagramParser::getUrl(),
            'User-Agent' => 'Mozilla/5.0',
        ];

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
            'headers' => $this->httpHeaders,
        ]);

        // Make cookies array
        $cookiesArray = array_reduce($this->cookies->toArray(), function ($carry, $item) {
            $carry[$item['Name']] = $item['Value'];

            return $carry;
        }, []);

        // Login and get sessionid cookie
        $loginRequest = $this->httpClient->request('POST', '/accounts/login/ajax/', [
            'headers' => array_merge(
                $this->httpHeaders,
                ['X-CSRFToken' => $cookiesArray['csrftoken']]
            ),
            'form_params' => [
                'username' => $this->config['username'],
                'password' => $this->config['password']
            ]
        ]);

        $loginBody = json_decode((string)$loginRequest->getBody(), true);
        if ($loginBody['authenticated'] === false) {
            throw new SocialRssException('Failed to login');
        }

        $url = empty($username) ? '/' : '/' . $username;
        // Open URL with data as a logged in user
        $feedRequest = $this->httpClient->request('GET', $url, [
            'headers' => $this->httpHeaders
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

        return json_decode($instagramJson, true);
    }
}
