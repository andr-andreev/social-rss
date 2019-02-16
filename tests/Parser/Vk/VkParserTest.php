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

    public function setUp(): void
    {
        $this->parser = (new ParserFactory())
            ->create('vk', ['access_token' => '']);
        $this->feed = json_decode(file_get_contents(__DIR__ . '/../../fixtures/vk.json'), true);
    }

    public function testParseFeed(): void
    {
        $parsedFeed = $this->parser->parseFeed($this->feed);

        $this->assertCount(count($this->feed['items']), $parsedFeed->posts);

        foreach ($parsedFeed->posts as $item) {
            $this->assertStringStartsWith('https://vk.com/', $item->link);
            $this->assertStringStartsWith('https://pp.userapi.com/', $item->author->avatar);
            $this->assertStringStartsWith('https://vk.com/', $item->author->link);

            if ($item->quote) {
                $this->assertStringStartsWith('https://vk.com/', $item->quote->link);
            }
        }
    }
}
