<?php
declare(strict_types = 1);


namespace SocialRss\Format;

use SocialRss\ParsedFeed\BaseParsedFeedCollection;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlFormat
 *
 * @package SocialRss\Format
 */
class YamlFormat implements FormatInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function format(BaseParsedFeedCollection $data): string
    {
        return Yaml::dump($data->toArray(), 3);
    }
}
