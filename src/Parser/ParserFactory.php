<?php
declare(strict_types=1);

namespace SocialRss\Parser;

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
     * @throws \InvalidArgumentException
     */
    protected function createParser(string $type, array $config): ParserInterface
    {
        $map = parent::PARSERS;

        if (!isset($map[$type])) {
            throw new \InvalidArgumentException("No parser found for $type");
        }

        return new $map[$type]($config);
    }

    /**
     * @return array
     */
    public function getParsersList(): array
    {
        return parent::PARSERS;
    }
}
