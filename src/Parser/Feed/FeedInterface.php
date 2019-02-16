<?php
declare(strict_types=1);


namespace SocialRss\Parser\Feed;

interface FeedInterface
{
    public function getItems(): array;
}
