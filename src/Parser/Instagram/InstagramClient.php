<?php
declare(strict_types=1);


namespace SocialRss\Parser\Instagram;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
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
     * @throws \RuntimeException
     */
    public function __construct(array $config)
    {
        $cookieFile = dirname(__DIR__, 3) . '/data/instagramCookie';

        $this->cookies = new FileCookieJar($cookieFile);
        $this->httpClient = new Client(['base_uri' => InstagramParser::getUrl(), 'cookies' => $this->cookies]);
        $this->httpHeaders = [
            'Referer' => InstagramParser::getUrl(),
            'User-Agent' => 'Mozilla/5.0',
        ];

        $this->config = $config;
    }

    /**
     * @param string $username
     * @return array
     * @throws SocialRssException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getFeed(string $username): array
    {
        // Due to new Instagram API update there is no ability to get users feed via the API
        // (deprecation of /users/self/feed endpoint).
        // https://www.instagram.com/developer/changelog/

        // Open instagram homepage
        $homepageRequest = $this->httpClient->request('GET', '/', [
            'headers' => $this->httpHeaders,
        ]);

        $instagramJson = $this->findEmbeddedData((string)$homepageRequest->getBody());
        if (!empty($instagramJson['entry_data']['LandingPage'])) {
            $this->login();
        }

        $url = empty($username) ? '/' : '/' . $username;
        // Open URL with data as a logged in user
        $feedRequest = $this->httpClient->request('GET', $url, [
            'headers' => $this->httpHeaders
        ]);

        // Find JSON data in the script tag
        $instagramJson = $this->findEmbeddedData((string)$feedRequest->getBody());
        if (!$instagramJson) {
            throw new SocialRssException('Failed to find data');
        }

        return $instagramJson;
    }

    /**
     * @param string $text
     * @return array|null
     */
    protected function findEmbeddedData(string $text): ?array
    {
        $reResult = preg_match(
            "/<script.*>window\\._sharedData = (.*?);<\\/script>/",
            $text,
            $matches
        );
        if ($reResult === 0) {
            return null;
        }

        return json_decode($matches[1], true);
    }

    protected function login(): void
    {
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
    }
}
