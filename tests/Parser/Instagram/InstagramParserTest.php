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
    private $feeds;

    private $fixtures = [
        __DIR__ . '/../../fixtures/instagram.json',
        __DIR__ . '/../../fixtures/instagram_user.json',
    ];

    public function setUp()
    {
        $this->parser = (new ParserFactory())->create('instagram', []);
        $this->feeds = array_map(function ($fixture) {
            return json_decode(file_get_contents($fixture), true);
        }, $this->fixtures);
    }

    public function testParseFeed()
    {
        // test when no caption provided
        // unset($feed[0]['caption']);

        foreach ($this->feeds as $feed) {
            $parsedFeed = $this->parser->parseFeed($feed);

            $this->assertNotEmpty($parsedFeed->getTitle());
            $this->assertNotEmpty($parsedFeed->getLink());
            $this->assertNotEmpty($parsedFeed->getItems());

//            $this->assertCount(count($feed), $parsedFeed->getItems());

            foreach ($parsedFeed->getItems() as $item) {
                $this->assertNotEmpty($item->getTitle());
                $this->assertStringStartsWith('https://www.instagram.com/', $item->getLink());
                $this->assertNotEmpty($item->getContent());
                $this->assertNotEmpty($item->getDate());
                $this->assertInternalType('array', $item->getTags());
                $this->assertNotEmpty($item->getAuthor()->getName());
                $this->assertStringEndsWith('.jpg', $item->getAuthor()->getAvatar());
                $this->assertStringStartsWith('https://www.instagram.com/', $item->getAuthor()->getLink());
            }
        }
    }
}
