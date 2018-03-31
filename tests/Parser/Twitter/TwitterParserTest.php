<?php
declare(strict_types = 1);

namespace SocialRss\Parser\Twitter;

use PHPUnit\Framework\TestCase;
use SocialRss\Parser\ParserFactory;
use SocialRss\Parser\ParserInterface;

/**
 * Class TwitterParserTest
 *
 * @package SocialRss\Parser\Twitter
 */
class TwitterParserTest extends TestCase
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

    public function testParseFeed(): void
    {
        $parsedFeed = $this->parser->parseFeed($this->feed);

        $this->assertNotEmpty($parsedFeed->getTitle());
        $this->assertNotEmpty($parsedFeed->getLink());
        $this->assertNotEmpty($parsedFeed->getItems());

        $this->assertCount(count($this->feed), $parsedFeed->getItems());

        foreach ($parsedFeed->getItems() as $item) {
            $this->assertNotEmpty($item->getTitle());
            $this->assertStringStartsWith('https://twitter.com/', $item->getLink());
            $this->assertNotEmpty($item->getContent());
            $this->assertNotEmpty($item->getDate());
            $this->assertNotEmpty($item->getAuthor()->getName());
            $this->assertStringStartsWith('https://pbs.twimg.com/profile_images/', $item->getAuthor()->getAvatar());
            $this->assertStringStartsWith('https://twitter.com/', $item->getAuthor()->getLink());

            if ($item->getQuote()) {
                $this->assertNotEmpty($item->getQuote()->getTitle());
                $this->assertStringStartsWith('https://twitter.com/', $item->getQuote()->getLink());
                $this->assertNotEmpty($item->getQuote()->getContent());
            }
        }
    }
}
