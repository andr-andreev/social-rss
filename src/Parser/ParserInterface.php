<?php
declare(strict_types = 1);


namespace SocialRss\Parser;

/**
 * Interface ParserInterface
 * @package SocialRss\Parser
 */
interface ParserInterface
{
    /**
     * ParserInterface constructor.
     * @param $config
     */
    public function __construct(array $config);

    /**
     * @param $username
     * @return mixed
     */
    public function getFeed(string $username): array;

    /**
     * @param $feed
     * @return mixed
     */
    public function parseFeed(array $feed): array;
}
