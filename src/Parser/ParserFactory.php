<?php
declare(strict_types=1);

namespace SocialRss\Parser;

class ParserFactory extends FactoryMethod
{
    protected function createParser(string $type, array $config): ParserInterface
    {
        $map = parent::PARSERS;

        if (!isset($map[$type])) {
            throw new \InvalidArgumentException("No parser found for $type");
        }

        return new $map[$type]($config);
    }

    public function getParsersList(): array
    {
        return parent::PARSERS;
    }
}
