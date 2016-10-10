<?php


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
    public function __construct($config);

    /**
     * @param $username
     * @return mixed
     */
    public function getFeed($username);

    /**
     * @param $feed
     * @return mixed
     */
    public function parseFeed($feed);
}
