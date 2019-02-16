<?php

declare(strict_types=1);

namespace SocialRss\Data;

use Spatie\DataTransferObject\DataTransferObject;

class FeedData extends DataTransferObject
{
    /** @var string */
    public $title;

    /** @var string */
    public $link;

    /** @var \SocialRss\Data\PostData[] */
    public $posts;
}
