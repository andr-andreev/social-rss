<?php
declare(strict_types=1);


namespace SocialRss\Parser\Client;

/**
 * Interface ClientInterface
 * @package SocialRss\Parser\Client
 */
interface ClientInterface
{
    /**
     * ClientInterface constructor.
     * @param array $config
     */
    public function __construct(array $config);

    /**
     * @param string $username
     * @return array
     */
    public function getFeed(string $username): array;
}
