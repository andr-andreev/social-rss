<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\ParsedFeed\ParsedFeedItem;

/**
 * Interface PostInterface
 *
 * @package SocialRss\Parser\Vk\Post
 */
interface PostInterface
{
    public function getTitle(): string;

    public function getLink(): string;

    public function getDescription(): string;

    /**
     * @return array|null|ParsedFeedItem
     */
    public function getQuote(): ?ParsedFeedItem;
}
