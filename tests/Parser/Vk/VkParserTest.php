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
            ->create('vk', ['access_token' => '']);
        $this->feed = json_decode(file_get_contents(__DIR__ . '/../../fixtures/vk.json'), true);
    }

    public function testParseFeed(): void
    {
        $parsedFeed = $this->parser->parseFeed($this->feed);

        $this->assertNotEmpty($parsedFeed->getTitle());
        $this->assertNotEmpty($parsedFeed->getLink());
        $this->assertNotEmpty($parsedFeed->getItems());

        // $this->assertCount(9, $parsedFeed['items']);

        foreach ($parsedFeed->getItems() as $item) {
            $this->assertNotEmpty($item->getTitle());
            $this->assertStringStartsWith('https://vk.com/', $item->getLink());
            $this->assertNotEmpty($item->getContent());
            $this->assertNotEmpty($item->getDate());
            $this->assertNotEmpty($item->getAuthor()->getName());
            $this->assertStringStartsWith('https://pp.userapi.com/', $item->getAuthor()->getAvatar());
            $this->assertStringStartsWith('https://vk.com/', $item->getAuthor()->getLink());

            if ($item->getQuote()) {
                $this->assertNotEmpty($item->getQuote()->getTitle());
                $this->assertStringStartsWith('https://vk.com/', $item->getQuote()->getLink());
                $this->assertNotEmpty($item->getQuote()->getContent());
            }
        }
    }
}
