<?php

declare(strict_types=1);


namespace SocialRss\Data;

use Spatie\DataTransferObject\DataTransferObject;

class AuthorData extends DataTransferObject
{
    /** @var string */
    public $name;

    /** @var string */
    public $avatar;

    /** @var string */
    public $link;
}
