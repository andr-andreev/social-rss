<?php


namespace SocialRss\Parser;

interface ParserInterface
{
    public function __construct($config);

    public function getFeed();

    public function parseFeed($feed);
}
