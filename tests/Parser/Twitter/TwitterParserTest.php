<?php
declare(strict_types=1);

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

    public function setUp(): void
    {
        $this->parser = (new ParserFactory())->create('twitter', []);
        $this->feed = json_decode(file_get_contents(__DIR__ . '/../../fixtures/twitter.json'), true);
    }

    public function testParseFeed(): void
    {
        $parsedFeed = $this->parser->parseFeed($this->feed);

        $this->assertCount(count($this->feed), $parsedFeed->posts);

        foreach ($parsedFeed->posts as $item) {
            $this->assertStringStartsWith('https://twitter.com/', $item->link);
            $this->assertStringStartsWith('https://pbs.twimg.com/profile_images/', $item->author->avatar);
            $this->assertStringStartsWith('https://twitter.com/', $item->author->link);

            if ($item->quote) {
                $this->assertStringStartsWith('https://twitter.com/', $item->quote->link);
            }
        }
    }
}
