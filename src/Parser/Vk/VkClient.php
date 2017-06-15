<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk;

use SocialRss\Exception\SocialRssException;
use SocialRss\Parser\Client\ClientInterface;
use VK\VK;

/**
 * Class VkClient
 * @package SocialRss\Parser\Vk
 */
class VkClient implements ClientInterface
{
    const API_METHOD_HOME = 'newsfeed.get';
    const API_METHOD_USER = 'wall.get';
    const API_PARAMETERS = ['count' => 100, 'extended' => 1];

    const CONFIG_DEFAULT = [
        'app_id' => '',
        'api_secret' => '',
        'access_token' => '',
    ];

    private $vkClient;


    /**
     * InstagramClient constructor.
     *
     * @param $config
     * @throws \VK\VKException
     */
    public function __construct(array $config)
    {
        $vkConfig = array_merge(self::CONFIG_DEFAULT, $config);
        $this->vkClient = new VK($vkConfig['app_id'], $vkConfig['api_secret'], $vkConfig['access_token']);
    }

    /**
     * @param $username
     * @return array
     * @throws SocialRssException
     */
    public function getFeed(string $username): array
    {
        if (empty($username)) {
            $method = self::API_METHOD_HOME;
            $parameters = self::API_PARAMETERS;
        } else {
            $method = self::API_METHOD_USER;
            $parameters = array_merge(self::API_PARAMETERS, ['domain' => $username]);
        }
        $socialFeed = $this->vkClient->api($method, $parameters);

        if (isset($socialFeed['error'])) {
            throw new SocialRssException($socialFeed['error']['error_msg']);
        }

        return $socialFeed['response'];
    }
}
