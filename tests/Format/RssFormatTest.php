<?php


namespace SocialRss\Format;

use SocialRss\Parser\Parser;

/**
 * Class RssFormatTest
 * @package SocialRss\Format
 */
class RssFormatTest extends \PHPUnit_Framework_TestCase
{
    public function testRssFormat()
    {
        $parser = new Parser('twitter', [
            'consumer_key' => '',
            'consumer_secret' => '',
            'oauth_access_token' => '',
            'oauth_access_token_secret' => '',
        ]);
        $writer = new Format('rss');

        $feed = json_decode(file_get_contents(__DIR__ . '/../fixtures/twitter.json'), true);
        $parsedFeed = $parser->parseFeed($feed);

        $out = $writer->format($parsedFeed);

        $lines = explode("\n", $out);

        $this->assertContains('rss version="2.0"', $lines[1]);
    }
}
