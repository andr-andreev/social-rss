<?php
declare(strict_types = 1);

namespace SocialRss\Parser\Instagram;

use SocialRss\Parser\Parser;

/**
 * Class InstagramParserTest
 * @package SocialRss\Parser\Instagram
 */
class InstagramParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParseFeed()
    {
        $parser = new Parser('instagram', ['username' => '', 'password' => '']);
        $feed = json_decode(file_get_contents(__DIR__ . '/../../fixtures/instagram.json'), true);

        // test when no caption provided
//        unset($feed[0]['caption']);

        $parsedFeed = $parser->parseFeed($feed);

        $this->assertNotEmpty($parsedFeed['title']);
        $this->assertNotEmpty($parsedFeed['link']);
        $this->assertNotEmpty($parsedFeed['items']);

        $this->assertCount(count($feed), $parsedFeed['items']);

        foreach ($parsedFeed['items'] as $item) {
            $this->assertNotEmpty($item['title']);
            $this->assertStringStartsWith('https://www.instagram.com/', $item['link']);
            $this->assertNotEmpty($item['content']);
            $this->assertNotEmpty($item['date']);
            $this->assertInternalType('array', $item['tags']);
            $this->assertNotEmpty($item['author']['name']);
            $this->assertStringEndsWith('.jpg', $item['author']['avatar']);
            $this->assertStringStartsWith('https://www.instagram.com/', $item['author']['link']);
        }
    }
}
