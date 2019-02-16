<?php
declare(strict_types=1);


namespace SocialRss\Parser;

use SocialRss\Data\FeedData;

abstract class AbstractParser implements ParserInterface
{
    public function parseFeed(array $feed): FeedData
    {
        $rawFeed = $this->getFeedParser($feed);
        $parsedFeed = $this->getParsedFeed(static::getName(), static::getUrl());

        foreach (array_filter($rawFeed->getItems()) as $item) {
            $parsedFeed->posts[] = $this->parsePost($item);
        }

        return $parsedFeed;
    }

    abstract public function parsePost(array $item);

    public function getParsedFeed(string $title, string $link): FeedData
    {
        return new FeedData([
            'title' => $title,
            'link' => $link,
            'posts' => [],
        ]);
    }
}
