<?php
declare(strict_types=1);


namespace SocialRss\Parser;

use SocialRss\ParsedFeed\BaseParsedFeedCollection;
use SocialRss\ParsedFeed\ParsedFeedItem;
use SocialRss\ParsedFeed\ParsedFeedItemAuthor;
use SocialRss\Parser\Feed\BaseFeed;
use SocialRss\Parser\Feed\FeedInterface;

/**
 * Class AbstractParser
 * @package SocialRss\Parser
 */
abstract class AbstractParser implements ParserInterface
{
    /**
     * @param array $feed
     * @return array|BaseParsedFeedCollection
     */
    public function parseFeed(array $feed): BaseParsedFeedCollection
    {
        $rawFeed = $this->getFeedParser($feed);
        $parsedFeed = $this->getParsedFeed(static::getName(), static::getUrl());

        foreach ($rawFeed->getItems() as $item) {
            /** @var  $feedItem */
            $parsedFeedItem = $this->parseFeedItem($item);

            $parsedFeed->addItem($parsedFeedItem);
        }

        return $parsedFeed;
    }

    /**
     * @param $item
     * @return ParsedFeedItem
     */
    public function parseFeedItem($item): ParsedFeedItem
    {
        $feedItem = $this->createFeedItemParser($item);

        return new ParsedFeedItem(
            $feedItem->getTitle(),
            $feedItem->getLink(),
            $feedItem->getContent(),
            $feedItem->getQuote(),
            $feedItem->getDate(),
            $feedItem->getTags(),
            new ParsedFeedItemAuthor(
                $feedItem->getAuthorName(),
                $feedItem->getAuthorAvatar(),
                $feedItem->getAuthorLink()
            )
        );
    }

    /**
     * @param string $title
     * @param string $link
     * @return BaseParsedFeedCollection
     */
    public function getParsedFeed(string $title, string $link): BaseParsedFeedCollection
    {
        return new BaseParsedFeedCollection($title, $link);
    }

    /**
     * @param array $feed
     * @return FeedInterface
     */
    public function getFeedParser(array $feed): FeedInterface
    {
        return new BaseFeed($feed);
    }
}
