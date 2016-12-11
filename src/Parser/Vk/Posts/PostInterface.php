<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk\Posts;

/**
 * Interface PostInterface
 *
 * @package SocialRss\Parser\Vk\Posts
 */
interface PostInterface
{
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
    public function getDescription(): string;

    /**
     * @return array
     */
    public function getQuote(): array;
}
