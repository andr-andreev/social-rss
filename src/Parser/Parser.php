<?php
declare(strict_types = 1);

namespace SocialRss\Parser;

use SocialRss\Exception\SocialRssException;

/**
 * Class Parser
 * @package SocialRss\Parser
 */
class Parser
{
    const PARSERS_MAP = [
        'instagram' => Instagram\InstagramParser::class,
        'twitter' => Twitter\TwitterParser::class,
        'vk' => Vk\VkParser::class,
    ];

    /** @var ParserInterface */
    private $parser;

    protected $socialFeed;
    protected $feed;


    /**
     * Parser constructor.
     * @param $parser
     * @param $config
     * @throws SocialRssException
     */
    public function __construct($parser, $config)
    {
        $map = self::PARSERS_MAP;

        if (!isset($map[$parser])) {
            throw new SocialRssException("No parser found for $parser source");
        }

        $this->parser = new $map[$parser]($config);
    }

    /**
     * @param string $username
     * @return mixed
     */
    public function getFeed($username = '')
    {
        return $this->parser->getFeed($username);
    }

    /**
     * @param $feed
     * @return mixed
     */
    public function parseFeed(array $feed)
    {
        return $this->parser->parseFeed($feed);
    }
}
