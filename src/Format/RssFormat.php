<?php
declare(strict_types = 1);


namespace SocialRss\Format;

use SocialRss\Parser\ParserTrait;
use Zend\Feed\Writer\Feed;

/**
 * Class RssFormat
 *
 * @package SocialRss\Format
 */
class RssFormat implements FormatInterface
{
    use ParserTrait;

    /**
     * @param $data
     * @return mixed
     */
    public function format(array $data): string
    {
        $feed = new Feed;

        $feed->setTitle($data['title']);
        $feed->setDescription($data['title']);
        $feed->setLink($data['link']);

        $items = $this->processData($data);

        foreach ($items as $item) {
            $entry = $feed->createEntry();

            $entry->setTitle($item['title']);
            $entry->setLink($item['link']);
            $entry->addAuthor($item['author']);
            $entry->addCategories($item['categories']);
            $entry->setDateCreated($item['dateCreated']);
            $entry->setDescription($item['description']);

            $feed->addEntry($entry);
        }

        return $feed->export('rss');
    }

    /**
     * @param $data
     * @return array
     */
    private function processData(array $data): array
    {
        return array_map(
            function ($item) {
                return [
                'title' => $item['title'],
                'link' => $item['link'],
                'author' => [
                    'name' => $item['author']['name']
                ],
                    'categories' => array_map(
                        function ($tag) {
                            return ['term' => $tag];
                        }, $item['tags']
                    ),
                'dateCreated' => $item['date'],
                'description' => $this->makeBlock(
                    $this->makeAvatar($item),
                    $this->makeContent($item)
                ),
                ];
            }, $data['items']
        );
    }

    /**
     * @param $item
     * @return string
     */
    private function makeAvatar($item): string
    {
        return $this->makeImg($item['author']['avatar'], $item['author']['link']);
    }

    /**
     * @param $item
     * @return string
     */
    private function makeContent($item): string
    {
        if (empty($item['quote'])) {
            return $item['content'];
        }

        $quoteAuthor = $this->makeLink($item['quote']['link'], $item['quote']['title']);
        return $item['content'] . "<blockquote>{$quoteAuthor}<br>{$item['quote']['content']}</blockquote>";
    }
}
