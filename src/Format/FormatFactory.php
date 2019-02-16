<?php
declare(strict_types = 1);

namespace SocialRss\Format;

class FormatFactory extends FactoryMethod
{
    protected function createFormat(string $type): FormatInterface
    {
        $map = parent::FORMATS;

        if (!isset($map[$type])) {
            throw new \InvalidArgumentException("Unknown output format: $type");
        }

        return new $map[$type]();
    }
}
