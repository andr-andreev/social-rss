<?php
declare(strict_types = 1);


namespace SocialRss\Format;

use SocialRss\Parser\ParserFactory;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlFormatTest
 * @package SocialRss\Format
 */
class YamlFormatTest extends \PHPUnit_Framework_TestCase
{
    public function testYamlFormat()
    {
        $parser = (new ParserFactory())->create('twitter', []);
        $writer = (new FormatFactory())->create('yaml');

        $feed = json_decode(file_get_contents(__DIR__ . '/../fixtures/twitter.json'), true);
        $parsedFeed = $parser->parseFeed($feed);

        $out = $writer->format($parsedFeed);

        $this->assertInternalType('array', Yaml::parse($out));
    }
}
