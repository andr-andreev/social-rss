<?php
declare(strict_types = 1);


namespace SocialRss\Parser;

use SocialRss\ParsedFeed\BaseParsedFeedCollection;
use SocialRss\Parser\Feed\FeedInterface;
use SocialRss\Parser\FeedItem\FeedItemInterface;

/**
 * Interface ParserInterface
 *
 * @package SocialRss\Parser
 */
interface ParserInterface
{
    /**
     * ParserInterface constructor.
     *
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
    public function parseFeed(array $feed): BaseParsedFeedCollection;

    /**
     * @param array $feed
     * @return FeedInterface
     */
    public function getFeedParser(array $feed): FeedInterface;

    /**
     * @param array $item
     * @return FeedItemInterface
     */
    public function createFeedItemParser(array $item): FeedItemInterface;


    /**
     * @return string
     */
    public static function getName(): string;

    /**
     * @return string
     */
    public static function getUrl(): string;
}
