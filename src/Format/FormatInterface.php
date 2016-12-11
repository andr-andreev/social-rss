<?php
declare(strict_types = 1);


namespace SocialRss\Format;

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
    public function format(array $data): string;
}
