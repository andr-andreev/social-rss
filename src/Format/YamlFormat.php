<?php


namespace SocialRss\Format;

use Symfony\Component\Yaml\Yaml;

class YamlFormat implements FormatInterface
{
    use \SocialRss\Parser\ParserTrait;

    public function format($data)
    {
        return Yaml::dump($data, 3);
    }
}
