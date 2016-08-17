<?php


namespace SocialRss\Format;

/**
 * Interface FormatInterface
 * @package SocialRss\Format
 */
interface FormatInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function format($data);
}
