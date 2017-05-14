<?php
declare(strict_types = 1);


namespace SocialRss\Format;

use SocialRss\ParsedFeed\BaseParsedFeedCollection;

/**
 * Interface FormatInterface
 *
 * @package SocialRss\Format
 */
interface FormatInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function format(BaseParsedFeedCollection $data): string;
}
