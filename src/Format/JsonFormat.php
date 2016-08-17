<?php


namespace SocialRss\Format;

class JsonFormat implements FormatInterface
{
    use \SocialRss\Parser\ParserTrait;

    public function format($data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
