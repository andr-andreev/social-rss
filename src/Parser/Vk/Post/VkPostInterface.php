<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\Data\PostData;

interface VkPostInterface
{
    public function getTitle(): string;

    public function getLink(): string;

    public function getDescription(): string;

    public function getQuote(): ?PostData;
}
