<?php

namespace SocialRss\Parser;

use SocialRss\Exception\SocialRssException;

class Parser
{
    const PARSERS_MAP = [
        'instagram' => Instagram\InstagramParser::class,
        'twitter' => Twitter\TwitterParser::class,
        'vk' => Vk\VkParser::class,
    ];

    private $parser;

    protected $socialFeed;
    protected $feed;


    public function __construct($parser, $config)
    {
        $map = self::PARSERS_MAP;

        if (!isset($map[$parser])) {
            throw new SocialRssException("No parser found for $parser source");
        }

        $this->parser = new $map[$parser]($config);
    }

    public function getFeed()
    {
        return $this->parser->getFeed();
    }

    public function parseFeed($feed)
    {
        return $this->parser->parseFeed($feed);
    }
}
