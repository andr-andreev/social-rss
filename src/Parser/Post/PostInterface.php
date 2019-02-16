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
    public function __construct(array $item);

    public function getTitle(): string;

    public function getLink(): string;

    public function getContent(): string;

    public function getDate(): \DateTime;

    public function getTags(): array;

    public function getAuthorName(): string;

    public function getAuthorAvatar();

    public function getAuthorLink(): string;

    public function getQuote(): ?PostData;
}
