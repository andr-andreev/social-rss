<?php
declare(strict_types = 1);


namespace SocialRss\Parser;

use SocialRss\Data\FeedData;
use SocialRss\Parser\Feed\FeedInterface;

interface ParserInterface
{
    public function __construct(array $config);

    public function getFeed(string $username): array;

    /**
     * @param $feed
     * @return mixed
     */
    public function parseFeed(array $feed): BaseParsedFeedCollection;

    public function getFeedParser(array $feed): FeedInterface;

    public static function getName(): string;

    public static function getUrl(): string;
}
