<?php

namespace SocialRss\Parser\Twitter;

class VkParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParseFeed()
    {
        $parser = new \SocialRss\Parser\Parser('vk', ['app_id' => '', 'api_secret' => '', 'access_token' => '']);
        $feed = json_decode(file_get_contents(__DIR__ . '/../../fixtures/vk.json'), true);

        $parsedFeed = $parser->parseFeed($feed);

        $this->assertNotEmpty($parsedFeed['title']);
        $this->assertNotEmpty($parsedFeed['link']);
        $this->assertNotEmpty($parsedFeed['items']);

//        $this->assertCount(9, $parsedFeed['items']);

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
