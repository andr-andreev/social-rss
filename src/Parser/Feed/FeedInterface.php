<?php
declare(strict_types=1);


namespace SocialRss\Parser\Feed;


/**
 * Interface FeedInterface
 * @package SocialRss\Parser\Feed
 */
interface FeedInterface
{

    /**
     * FeedInterface constructor.
     * @param array $feed
     */
    public function __construct(array $feed);

    /**
     * @return array
     */
    public function getItems(): array;

}