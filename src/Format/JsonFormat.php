<?php
declare(strict_types = 1);


namespace SocialRss\Format;

use SocialRss\Data\FeedData;

class JsonFormat implements FormatInterface
{
    public function format(FeedData $data): string
    {
        return json_encode($data->toArray(), JSON_PRETTY_PRINT);
    }
}
