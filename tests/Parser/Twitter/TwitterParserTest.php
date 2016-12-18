<?php
declare(strict_types = 1);

namespace SocialRss\Parser\Twitter;

use SocialRss\Parser\ParserFactory;
use SocialRss\Parser\ParserInterface;

/**
 * Class TwitterParserTest
 *
 * @package SocialRss\Parser\Twitter
 */
class TwitterParserTest extends \PHPUnit_Framework_TestCase
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
        $this->parser = (new ParserFactory())->create('twitter', []);
        $this->feed = json_decode(file_get_contents(__DIR__ . '/../../fixtures/twitter.json'), true);
    }

    public function testParseFeed()
    {
        $parsedFeed = $this->parser->parseFeed($this->feed);

        $this->assertNotEmpty($parsedFeed['title']);
        $this->assertNotEmpty($parsedFeed['link']);
        $this->assertNotEmpty($parsedFeed['items']);

        $this->assertCount(count($this->feed), $parsedFeed['items']);

        foreach ($parsedFeed['items'] as $item) {
            $this->assertNotEmpty($item['title']);
            $this->assertStringStartsWith('https://twitter.com/', $item['link']);
            $this->assertNotEmpty($item['content']);
            $this->assertNotEmpty($item['date']);
            $this->assertInternalType('array', $item['tags']);
            $this->assertNotEmpty($item['author']['name']);
            $this->assertStringStartsWith('https://pbs.twimg.com/profile_images/', $item['author']['avatar']);
            $this->assertStringStartsWith('https://twitter.com/', $item['author']['link']);
            //            $this->assertInternalType('array', $item['quote']);

            if (!empty($item['quote'])) {
                $this->assertNotEmpty($item['quote']['title']);
                $this->assertStringStartsWith('https://twitter.com/', $item['quote']['link']);
                $this->assertNotEmpty($item['quote']['content']);
            }
        }
    }
}
