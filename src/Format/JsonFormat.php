<?php


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
    public function format($data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
