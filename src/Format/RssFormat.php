<?php
declare(strict_types = 1);


namespace SocialRss\Format;

use SocialRss\Data\FeedData;
use SocialRss\Data\PostData;
use SocialRss\Helper\Html;
use Zend\Feed\Writer\Feed;

class RssFormat implements FormatInterface
{
    public function format(FeedData $data): string
    {
        $feed = new Feed;

        $feed->setTitle($data->title);
        $feed->setDescription($data->title);
        $feed->setLink($data->link);

        foreach ($data->posts as $item) {
            $entry = $feed->createEntry();

            $entry->setTitle($item->title);
            $entry->setLink($item->link);
            $entry->addAuthor([
                'name' => $item->author ? $item->author->name : ''
            ]);
            $entry->addCategories(array_map(function ($tag) {
                return ['term' => $tag];
            }, $item->tags));
            $entry->setDateCreated($item->date);
            $entry->setDescription(Html::makeBlock(
                $item->author
                    ? Html::makeAvatar($item->author->avatar, $item->author->link)
                    : '',
                $this->makeContent($item)
            ));

            $feed->addEntry($entry);
        }

        return $feed->export('rss');
    }

    protected function makeContent(PostData $item): string
    {
        $out = $item->content;

        if ($item->quote) {
            $quoteAuthorLink = Html::link($item->quote->link, $item->quote->title);
            $quoteContent = $item->quote->content;

            $out .= Html::blockquote(<<<HTML
{$quoteAuthorLink}<br>
{$quoteContent}
HTML
            );
        }

        return $out;
    }
}
