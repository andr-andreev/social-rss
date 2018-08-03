<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk;

use SocialRss\Exception\SocialRssException;
use SocialRss\Parser\Client\ClientInterface;
use VK\Client\VKApiClient;
use VK\Exceptions\VKApiException;
use VK\Exceptions\VKClientException;

/**
 * Class VkClient
 * @package SocialRss\Parser\Vk
 */
class VkClient implements ClientInterface
{
    protected const API_PARAMETERS = [
        'count' => 100,
        'extended' => 1,
    ];

    protected $vkClient;
    protected $token;

    /**
     * VkClient constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->vkClient = new VKApiClient('5.73');
        $this->token = $config['access_token'];
    }

    /**
     * @param $username
     * @return array
     * @throws VKClientException|SocialRssException
     */
    public function getFeed(string $username): array
    {
        try {
            if (empty($username)) {
                $socialFeed = $this->vkClient->newsfeed()->get($this->token, self::API_PARAMETERS);
            } else {
                $parameters = array_merge(self::API_PARAMETERS, ['domain' => $username]);

                $socialFeed = $this->vkClient->wall()->get($this->token, $parameters);
            }
        } catch (VKApiException $e) {
            throw new SocialRssException($e->getMessage());
        }

        return $socialFeed;
    }
}
