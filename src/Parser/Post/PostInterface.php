<?php
declare(strict_types=1);


namespace SocialRss\Parser\Post;

use SocialRss\Data\PostData;

interface PostInterface
{
    public function __construct(array $item);

    public function getTitle(): string;

    public function getLink(): string;

    public function getContent(): string;

    public function getDate(): \DateTime;

    public function getTags(): array;

    public function getAuthorName(): string;

    public function getAuthorAvatar(): string;

    public function getAuthorLink(): string;

    public function getQuote(): ?PostData;
}
