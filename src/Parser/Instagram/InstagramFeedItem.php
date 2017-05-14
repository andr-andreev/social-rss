<?php
declare(strict_types=1);


namespace SocialRss\Parser\Instagram;

use SocialRss\Helper\Html;
use SocialRss\ParsedFeed\ParsedFeedItem;
use SocialRss\Parser\FeedItem\FeedItemInterface;


/**
 * Class InstagramFeedItem
 * @package SocialRss\Parser\Instagram
 */
class InstagramFeedItem implements FeedItemInterface
{
    private $item;

    /**
     * InstagramFeedItem constructor.
     * @param array $item
     */
    public function __construct(array $item)
    {
        $this->item = $item;
    }

    /**
     * @param $item
     * @return string
     */
    private function parseContent(array $item): string
    {
        $location = $item['location']['name'];

        // Use image or video
        $media = ($item['is_video'] && isset($item['video_url'])) ? Html::video(
            $item['video_url'],
            $item['display_src']
        ) : Html::img($item['display_src']);

        // Match #hashtags
        $caption = Html::parseByPattern(
            '#',
            '<a href="https://www.instagram.com/explore/tags/{{string}}/">#{{string}}</a>',
            $item['caption']
        );

        // Match @mentions
        $caption = Html::parseByPattern(
            '@',
            '<a href="https://www.instagram.com/{{string}}/">@{{string}}</a>',
            $caption
        );

        $content = $location . PHP_EOL . $media . PHP_EOL . $caption;

        return nl2br(trim($content));
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->item['owner']['username'];
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return InstagramParser::getUrl() . "p/{$this->item['code']}/";
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->parseContent($this->item);
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return \DateTime::createFromFormat('U', strval($this->item['date']));
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return Html::getParsedByPattern('#{string}', $this->item['caption']);
    }

    /**
     * @return string
     */
    public function getAuthorName(): string
    {
        return $this->item['owner']['username'];
    }

    /**
     * @return mixed
     */
    public function getAuthorAvatar()
    {
        return $this->item['owner']['profile_pic_url'];
    }

    /**
     * @return string
     */
    public function getAuthorLink(): string
    {
        return InstagramParser::getUrl() . "{$this->item['owner']['username']}/";
    }

    /**
     * @return null|ParsedFeedItem
     */
    public function getQuote():?ParsedFeedItem
    {
        return null;
    }
}