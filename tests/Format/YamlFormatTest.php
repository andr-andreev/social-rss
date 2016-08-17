<?php


namespace SocialRss\Format;

use SocialRss\Parser\Parser;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlFormatTest
 * @package SocialRss\Format
 */
class YamlFormatTest extends \PHPUnit_Framework_TestCase
{
    public function testYamlFormat()
    {
        $parser = new Parser('twitter', [
            'consumer_key' => '',
            'consumer_secret' => '',
            'oauth_access_token' => '',
            'oauth_access_token_secret' => '',
        ]);
        $writer = new Format('yaml');

        $feed = json_decode(file_get_contents(__DIR__ . '/../fixtures/twitter.json'), true);
        $parsedFeed = $parser->parseFeed($feed);

        $out = $writer->format($parsedFeed);

        $this->assertInternalType('array', Yaml::parse($out));
    }
}
