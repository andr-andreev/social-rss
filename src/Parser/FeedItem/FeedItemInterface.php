<?php
declare(strict_types=1);


namespace SocialRss\Parser\FeedItem;

use SocialRss\ParsedFeed\ParsedFeedItem;


/**
 * Interface FeedItemInterface
 * @package SocialRss\Parser\FeedItem
 */
interface FeedItemInterface
{
    /**
     * FeedItemInterface constructor.
     * @param array $item
     */
    public function __construct(array $item);

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @return string
     */
    public function getLink(): string;

    /**
     * @return string
     */
    public function getContent(): string;

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime;

    /**
     * @return array
     */
    public function getTags(): array;

    /**
     * @return string
     */
    public function getAuthorName(): string;

    public function getAuthorAvatar();

    /**
     * @return string
     */
    public function getAuthorLink(): string;

    /**
     * @return null|ParsedFeedItem
     */
    public function getQuote():?ParsedFeedItem;
}