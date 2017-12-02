<?php
declare(strict_types=1);


namespace SocialRss\ParsedFeed;

/**
 * Class BaseParsedFeedCollection
 * @package SocialRss\ParsedFeed
 */
class BaseParsedFeedCollection extends \ArrayObject
{
    protected $title;
    protected $link;
    protected $items = [];

    /**
     * BaseParsedFeedCollection constructor.
     * @param string $title
     * @param string $link
     */
    public function __construct(string $title, string $link)
    {
        $this->title = $title;
        $this->link = $link;
    }

    /**
     * @param null|ParsedFeedItem $item
     */
    public function addItem(?ParsedFeedItem $item)
    {
        if (null === $item) {
            return;
        }

        $this->items[] = $item;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @return ParsedFeedItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $itemToArray = function (ParsedFeedItem $item) {
            $date = $item->getDate();
            $author = $item->getAuthor();

            $dateOut = '';
            if ($date) {
                $dateOut = $date->format('r');
            }

            $authorOut = [];
            if ($author) {
                $authorOut = [
                    'name' => $author->getName(),
                    'avatar' => $author->getAvatar(),
                    'link' => $author->getLink(),
                ];
            }

            return [
                'title' => $item->getTitle(),
                'link' => $item->getLink(),
                'content' => $item->getContent(),
                'date' => $dateOut,
                'tags' => $item->getTags(),
                'author' => $authorOut,
            ];
        };

        return [
            'title' => $this->getTitle(),
            'link' => $this->getLink(),
            'items' => array_map(function (ParsedFeedItem $item) use ($itemToArray) {
                $quoteOut = [];
                if ($item->getQuote()) {
                    $quoteOut = $itemToArray($item->getQuote());
                }

                return array_merge($itemToArray($item), ['quote' => $quoteOut]);
            }, $this->getItems()),
        ];
    }
}
