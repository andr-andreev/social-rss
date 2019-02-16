<?php
declare(strict_types = 1);

namespace SocialRss\Parser;

abstract class FactoryMethod
{
    protected const PARSERS = [
        'twitter' => Twitter\TwitterParser::class,
        'vk' => Vk\VkParser::class,
    ];

    abstract protected function createParser(string $type, array $config): ParserInterface;

    public function create(string $type, array $config): ParserInterface
    {
        return $this->createParser($type, $config);
    }
}
