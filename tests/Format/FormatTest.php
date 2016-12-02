<?php
declare(strict_types = 1);


namespace SocialRss\Format;

use SocialRss\Exception\SocialRssException;

/**
 * Class FormatTest
 * @package SocialRss\Format
 */
class FormatTest extends \PHPUnit_Framework_TestCase
{
    public function testFormatUnknown()
    {
        $this->expectException(SocialRssException::class);

        $writer = new Format('xml');
    }
}
