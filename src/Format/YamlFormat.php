<?php


namespace SocialRss\Format;

use SocialRss\Parser\ParserTrait;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlFormat
 * @package SocialRss\Format
 */
class YamlFormat implements FormatInterface
{
    use ParserTrait;

    /**
     * @param $data
     * @return mixed
     */
    public function format($data)
    {
        return Yaml::dump($data, 3);
    }
}
