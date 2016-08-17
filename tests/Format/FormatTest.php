<?php


namespace SocialRss\Format;

class FormatTest extends \PHPUnit_Framework_TestCase
{
    public function testFormatUnknown()
    {
        $this->expectException(\SocialRss\Exception\SocialRssException::class);

        $writer = new Format('xml');
    }
}
