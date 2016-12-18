<?php
declare(strict_types = 1);

namespace SocialRss\Parser;

/**
 * Class FactoryMethod
 *
 * @package SocialRss\Parser
 */
abstract class FactoryMethod
{
    const PARSERS = [
        'instagram' => Instagram\InstagramParser::class,
        'twitter' => Twitter\TwitterParser::class,
        'vk' => Vk\VkParser::class,
    ];

    /**
     * @param string $type
     * @param array $config
     * @return ParserInterface
     */
    abstract protected function createParser(string $type, array $config): ParserInterface;

    /**
     * @param string $type
     * @param array $config
     * @return ParserInterface
     */
    public function create(string $type, array $config): ParserInterface
    {
        return $this->createParser($type, $config);
    }
}
