<?php
declare(strict_types = 1);


namespace SocialRss\Format;


/**
 * Class FactoryMethodTest
 * @package SocialRss\Format
 */
class FactoryMethodTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateJsonFormat()
    {
        $factory = new FormatFactory();
        $result = $factory->create('json');

        $this->assertInstanceOf(JsonFormat::class, $result);
    }

    public function testCanCreateRssFormat()
    {
        $factory = new FormatFactory();
        $result = $factory->create('rss');

        $this->assertInstanceOf(RssFormat::class, $result);
    }

    public function testCanCreateYamlFormat()
    {
        $factory = new FormatFactory();
        $result = $factory->create('yaml');

        $this->assertInstanceOf(YamlFormat::class, $result);
    }

    public function testUnknownType()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new FormatFactory())->create('txt');
    }
}
