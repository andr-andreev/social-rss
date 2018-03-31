<?php
declare(strict_types = 1);

namespace SocialRss\Format;

use PHPUnit\Framework\TestCase;

/**
 * Class FactoryMethodTest
 *
 * @package SocialRss\Format
 */
class FactoryMethodTest extends TestCase
{
    public function testCanCreateJsonFormat(): void
    {
        $factory = new FormatFactory();
        $result = $factory->create('json');

        $this->assertInstanceOf(JsonFormat::class, $result);
    }

    public function testCanCreateRssFormat(): void
    {
        $factory = new FormatFactory();
        $result = $factory->create('rss');

        $this->assertInstanceOf(RssFormat::class, $result);
    }

    public function testUnknownType(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new FormatFactory())->create('txt');
    }
}
