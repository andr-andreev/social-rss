<?php
declare(strict_types = 1);

namespace SocialRss\Format;

/**
 * Class FormatFactory
 *
 * @package SocialRss\Format
 */
class FormatFactory extends FactoryMethod
{
    /**
     * @param string $type
     * @return FormatInterface
     */
    protected function createFormat(string $type): FormatInterface
    {
        $map = parent::FORMATS;

        if (!isset($map[$type])) {
            throw new \InvalidArgumentException("Unknown output format: $type");
        }

        return new $map[$type]();
    }
}
