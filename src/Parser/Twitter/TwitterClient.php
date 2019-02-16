<?php
declare(strict_types=1);


namespace SocialRss\Parser\Twitter;

use SocialRss\Exception\SocialRssException;
use SocialRss\Parser\Client\ClientInterface;

class TwitterClient implements ClientInterface
{
    protected const API_URL_HOME = 'https://api.twitter.com/1.1/statuses/home_timeline.json';
    protected const API_URL_USER = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    protected const API_PARAMETERS = ['count' => 100, 'tweet_mode' => 'extended', 'exclude_replies' => 'true'];

    protected const CONFIG_DEFAULT = [
        'consumer_key' => '',
        'consumer_secret' => '',
        'oauth_access_token' => '',
        'oauth_access_token_secret' => '',
    ];

    /** @var \TwitterAPIExchange */
    protected $twitterClient;

    public function __construct(array $config)
    {
        $twitterConfig = array_merge(self::CONFIG_DEFAULT, $config);

        $this->twitterClient = new \TwitterAPIExchange($twitterConfig);
    }

    /**
     * @throws \Exception
     * @throws SocialRssException
     */
    public function getFeed(string $username): array
    {
        if (empty($username)) {
            $url = self::API_URL_HOME;
            $parameters = self::API_PARAMETERS;
        } else {
            $url = self::API_URL_USER;
            $parameters = array_merge(self::API_PARAMETERS, ['screen_name' => $username]);
        }
        $queryArgs = '?' . http_build_query($parameters);

        $twitterJson = $this->twitterClient
            ->setGetfield($queryArgs)
            ->buildOauth($url, 'GET')
            ->performRequest();

        $feed = json_decode($twitterJson, true);

        if (isset($feed['errors'][0]['message'])) {
            throw new SocialRssException($feed['errors'][0]['message']);
        }

        return $feed;
    }
}
