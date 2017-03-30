<?php
declare(strict_types = 1);

namespace SocialRss\Parser\Vk;

use PHPUnit\Framework\TestCase;
use SocialRss\Parser\ParserFactory;
use SocialRss\Parser\ParserInterface;

/**
 * Class VkParserTest
 *
 * @package SocialRss\Parser\Vk
 */
class VkParserTest extends TestCase
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
        $this->parser = (new ParserFactory())
            ->create('vk', []);
        $this->feed = json_decode(file_get_contents(__DIR__ . '/../../fixtures/vk.json'), true);
    }

    public function testParseFeed()
    {
        $parsedFeed = $this->parser->parseFeed($this->feed);

        $this->assertNotEmpty($parsedFeed['title']);
        $this->assertNotEmpty($parsedFeed['link']);
        $this->assertNotEmpty($parsedFeed['items']);

        // $this->assertCount(9, $parsedFeed['items']);

        foreach ($parsedFeed['items'] as $item) {
            $this->assertNotEmpty($item['title']);
            $this->assertStringStartsWith('https://vk.com/', $item['link']);
            $this->assertArrayHasKey('content', $item);
            $this->assertNotEmpty($item['date']);
            $this->assertInternalType('array', $item['tags']);
            $this->assertNotEmpty($item['author']['name']);
            $this->assertContains('vk.', $item['author']['avatar']);
            $this->assertStringStartsWith('https://vk.com/', $item['author']['link']);
            $this->assertInternalType('array', $item['quote']);

            if (!empty($item['quote'])) {
                $this->assertNotEmpty($item['quote']['title']);
                $this->assertStringStartsWith('https://vk.com/', $item['quote']['link']);
                $this->assertNotEmpty($item['quote']['content']);
            }
        }
    }
}
