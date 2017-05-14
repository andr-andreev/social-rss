<?php
declare(strict_types = 1);


namespace SocialRss\Format;

use SocialRss\Helper\Html;
use SocialRss\ParsedFeed\BaseParsedFeedCollection;
use SocialRss\ParsedFeed\ParsedFeedItem;
use Zend\Feed\Writer\Feed;

/**
 * Class RssFormat
 *
 * @package SocialRss\Format
 */
class RssFormat implements FormatInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function format(BaseParsedFeedCollection $data): string
    {
        $feed = new Feed;

        $feed->setTitle($data->getTitle());
        $feed->setDescription($data->getTitle());
        $feed->setLink($data->getLink());

//        $items = $this->processData($data);

        foreach ($data->getItems() as $item) {
            $entry = $feed->createEntry();

            $entry->setTitle($item->getTitle());
            $entry->setLink($item->getLink());
            $entry->addAuthor([
                'name' => $item->getAuthor()->getName(),
            ]);
            $entry->addCategories(array_map(function ($tag) {
                return ['term' => $tag];
            }, $item->getTags()));
            $entry->setDateCreated($item->getDate());
            $entry->setDescription(Html::makeBlock(
                Html::makeAvatar($item->getAuthor()->getAvatar(), $item->getAuthor()->getLink()),
                $this->makeContent($item)
            ));

            $feed->addEntry($entry);
        }

        return $feed->export('rss');
    }

    /**
     * @param ParsedFeedItem $item
     * @return string
     */
    private function makeContent(ParsedFeedItem $item)
    {
        $out = $item->getContent();

        if ($item->getQuote()) {
            $quote = $item->getQuote();

            $quoteAuthorLink = Html::link($quote->getLink(), $quote->getTitle());
            $quoteContent = $quote->getContent();
            $out .= Html::blockquote("{$quoteAuthorLink}<br>{$quoteContent}");
        }

        return $out;
    }

}
