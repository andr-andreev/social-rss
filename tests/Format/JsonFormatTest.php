<?php
declare(strict_types = 1);


namespace SocialRss\Format;

use SocialRss\Parser\ParserFactory;

/**
 * Class JsonFormatTest
 *
 * @package SocialRss\Format
 */
class JsonFormatTest extends \PHPUnit_Framework_TestCase
{
    public function testJsonFormat()
    {
        $parser = (new ParserFactory())->create('twitter', []);
        $writer = (new FormatFactory())->create('json');

        $feed = json_decode(file_get_contents(__DIR__ . '/../fixtures/twitter.json'), true);
        $parsedFeed = $parser->parseFeed($feed);

        $out = $writer->format($parsedFeed);

        json_decode($out);
        $this->assertSame(JSON_ERROR_NONE, json_last_error());
    }
}
