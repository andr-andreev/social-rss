<?php
declare(strict_types = 1);


namespace SocialRss\Format;

use SocialRss\Data\FeedData;

interface FormatInterface
{
    public function format(FeedData $data): string;
}
