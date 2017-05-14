<?php
declare(strict_types = 1);


namespace SocialRss\Format;

use SocialRss\ParsedFeed\BaseParsedFeedCollection;

/**
 * Class JsonFormat
 *
 * @package SocialRss\Format
 */
class JsonFormat implements FormatInterface
{

    /**
     * @param $data
     * @return string
     */
    public function format(BaseParsedFeedCollection $data): string
    {
        return json_encode($data->toArray(), JSON_PRETTY_PRINT);
    }
}
