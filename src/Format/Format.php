<?php


namespace SocialRss\Format;

use SocialRss\Exception\SocialRssException;

/**
 * Class Format
 * @package SocialRss\Format
 */
class Format
{
    const FORMATS_MAP = [
        'rss' => RssFormat::class,
        'json' => JsonFormat::class,
        'yaml' => YamlFormat::class,
    ];

    private $writer;

    /**
     * Format constructor.
     * @param $format
     * @throws SocialRssException
     */
    public function __construct($format)
    {
        $map = self::FORMATS_MAP;

        if (!isset($map[$format])) {
            throw new  SocialRssException("Unknown output format: $format");
        }

        $this->writer = new $map[$format];
    }

    /**
     * @param $report
     * @return mixed
     */
    public function format($report)
    {
        return $this->writer->format($report);
    }
}
