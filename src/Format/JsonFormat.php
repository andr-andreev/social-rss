<?php
declare(strict_types = 1);


namespace SocialRss\Format;

use SocialRss\Parser\ParserTrait;

/**
 * Class JsonFormat
 * @package SocialRss\Format
 */
class JsonFormat implements FormatInterface
{
    use ParserTrait;

    /**
     * @param $data
     * @return string
     */
    public function format(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
