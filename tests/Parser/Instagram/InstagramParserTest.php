<?php
declare(strict_types = 1);

namespace SocialRss\Parser\Instagram;

use PHPUnit\Framework\TestCase;
use SocialRss\Parser\ParserFactory;
use SocialRss\Parser\ParserInterface;

/**
 * Class InstagramParserTest
 *
 * @package SocialRss\Parser\Instagram
 */
class InstagramParserTest extends TestCase
{
    /**
     * @var ParserInterface
     */
    private $parser;
    /**
     * @var array
     */
    private $feed;

    public function setUp()
    {
        $this->parser = (new ParserFactory())->create('instagram', []);
        $this->feed = json_decode(file_get_contents(__DIR__ . '/../../fixtures/instagram.json'), true);
    }

    public function testParseFeed()
    {
        // test when no caption provided
        // unset($feed[0]['caption']);

        $parsedFeed = $this->parser->parseFeed($this->feed);

        $this->assertNotEmpty($parsedFeed['title']);
        $this->assertNotEmpty($parsedFeed['link']);
        $this->assertNotEmpty($parsedFeed['items']);

        $this->assertCount(count($this->feed), $parsedFeed['items']);

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
