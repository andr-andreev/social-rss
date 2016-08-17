<?php

namespace SocialRss\Parser\Twitter;

class TwitterParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParseFeed()
    {
        $parser = new \SocialRss\Parser\Parser('twitter', [
            'consumer_key' => '',
            'consumer_secret' => '',
            'oauth_access_token' => '',
            'oauth_access_token_secret' => '',
        ]);
        $feed = json_decode(file_get_contents(__DIR__ . '/../../fixtures/twitter.json'), true);

        $parsedFeed = $parser->parseFeed($feed);

        $this->assertNotEmpty($parsedFeed['title']);
        $this->assertNotEmpty($parsedFeed['link']);
        $this->assertNotEmpty($parsedFeed['items']);

        $this->assertCount(count($feed), $parsedFeed['items']);

        foreach ($parsedFeed['items'] as $item) {
            $this->assertNotEmpty($item['title']);
            $this->assertStringStartsWith('https://twitter.com/', $item['link']);
            $this->assertNotEmpty($item['content']);
            $this->assertNotEmpty($item['date']);
            $this->assertInternalType('array', $item['tags']);
            $this->assertNotEmpty($item['author']['name']);
            $this->assertStringStartsWith('https://pbs.twimg.com/profile_images/', $item['author']['avatar']);
            $this->assertStringStartsWith('https://twitter.com/', $item['author']['link']);
            $this->assertInternalType('array', $item['quote']);

            if (!empty($item['quote'])) {
                $this->assertNotEmpty($item['quote']['title']);
                $this->assertStringStartsWith('https://twitter.com/', $item['quote']['link']);
                $this->assertNotEmpty($item['quote']['content']);
            }
        }
    }
}
