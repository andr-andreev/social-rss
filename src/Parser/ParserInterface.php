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

    public function getFeed();

    /**
     * @param $feed
     * @return mixed
     */
    public function parseFeed($feed);
}
