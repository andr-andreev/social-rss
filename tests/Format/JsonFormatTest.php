<?php
declare(strict_types = 1);


namespace SocialRss\Format;

use SocialRss\Parser\Parser;

/**
 * Class JsonFormatTest
 * @package SocialRss\Format
 */
class JsonFormatTest extends \PHPUnit_Framework_TestCase
{
    public function testJsonFormat()
    {
        $parser = new Parser('twitter', [
            'consumer_key' => '',
            'consumer_secret' => '',
            'oauth_access_token' => '',
            'oauth_access_token_secret' => '',
        ]);
        $writer = new Format('json');

        $feed = json_decode(file_get_contents(__DIR__ . '/../fixtures/twitter.json'), true);
        $parsedFeed = $parser->parseFeed($feed);

        $out = $writer->format($parsedFeed);

        json_decode($out);
        $this->assertSame(JSON_ERROR_NONE, json_last_error());
    }
}
