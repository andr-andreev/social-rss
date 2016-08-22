<?php


namespace SocialRss\Format;

use SocialRss\Parser\ParserTrait;
use Zend\Feed\Writer\Feed;

/**
 * Class RssFormat
 * @package SocialRss\Format
 */
class RssFormat implements FormatInterface
{
    use ParserTrait;

    /**
     * @param $data
     * @return mixed
     */
    public function format($data)
    {
        $feed = new Feed;

        $feed->setTitle($data['title']);
        $feed->setDescription($data['title']);
        $feed->setLink($data['link']);

        $items = array_reduce($data['items'], function ($carry, $item) {
            $quotedBlock = '';
            if (!empty($item['quote'])) {
                $author = $this->makeLink($item['quote']['link'], $item['quote']['title']);
                $quotedBlock = "<blockquote>" . $author . '<br>' . $item['quote']['content'] . "</blockquote>";
            }

            $avatar = $this->makeImg($item['author']['avatar'], $item['author']['link']);
            $content = $this->makeBlock($avatar, $item['content'] . $quotedBlock);

            $categories = array_map(function ($tag) {
                return ['term' => $tag];
            }, $item['tags']);

            $carry[] = [
                'title' => $item['title'],
                'link' => $item['link'],
                'author' => [
                    'name' => $item['author']['name']
                ],
                'categories' => $categories,
                'dateCreated' => $item['date'],
                'description' => $content,
            ];
            return $carry;
        });

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
}
