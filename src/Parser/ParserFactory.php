<?php
declare(strict_types = 1);

namespace SocialRss\Parser;

use SocialRss\Exception\SocialRssException;


/**
 * Class ParserFactory
 *
 * @package SocialRss\Parser
 */
class ParserFactory extends FactoryMethod
{
    /**
     * @param string $type
     * @param array $config
     * @return ParserInterface
     */
    protected function createParser(string $type, array $config): ParserInterface
    {
        $map = parent::PARSERS;

        if (!isset($map[$type])) {
            throw new \InvalidArgumentException("No parser found for $type");
        }

        return new $map[$type]($config);
    }
}