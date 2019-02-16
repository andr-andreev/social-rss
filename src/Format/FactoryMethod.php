<?php
declare(strict_types = 1);

namespace SocialRss\Format;

abstract class FactoryMethod
{
    protected const FORMATS = [
        'rss' => RssFormat::class,
        'json' => JsonFormat::class,
    ];

    abstract protected function createFormat(string $type): FormatInterface;

    public function create(string $type): FormatInterface
    {
        return $this->createFormat($type);
    }
}
