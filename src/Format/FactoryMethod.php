<?php
declare(strict_types = 1);

namespace SocialRss\Format;


/**
 * Class FactoryMethod
 * @package SocialRss\Format
 */
abstract class FactoryMethod
{
    const FORMATS = [
        'rss' => RssFormat::class,
        'json' => JsonFormat::class,
        'yaml' => YamlFormat::class,
    ];

    /**
     * @param string $type
     * @return FormatInterface
     */
    abstract protected function createFormat(string $type): FormatInterface;

    /**
     * @param string $type
     * @return FormatInterface
     */
    public function create(string $type): FormatInterface
    {
        return $this->createFormat($type);
    }
}