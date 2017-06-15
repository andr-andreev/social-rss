<?php
declare(strict_types=1);


namespace SocialRss\ParsedFeed;

/**
 * Class ParsedFeedItem
 * @package SocialRss\ParsedFeed
 */
class ParsedFeedItem
{
    public $title;
    public $link;
    public $content;
    public $date;
    public $tags;
    /** @var ParsedFeedItemAuthor */
    public $author;

    /**
     * ParsedFeedItem constructor.
     * @param $title
     * @param $link
     * @param $content
     * @param $quote
     * @param $date
     * @param $tags
     * @param $author
     */
    public function __construct(
        string $title,
        string $link,
        string $content,
        ?ParsedFeedItem $quote = null,
        \DateTime $date = null,
        array $tags = [],
        ParsedFeedItemAuthor $author = null
    ) {
        $this->title = $title;
        $this->link = $link;
        $this->content = $content;
        $this->quote = $quote;
        $this->date = $date;
        $this->tags = $tags;
        $this->author = $author;
    }

    /**
     * @return ParsedFeedItem|null
     */
    public function getQuote(): ?ParsedFeedItem
    {
        return $this->quote;
    }


    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link)
    {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content)
    {
        $this->content = $content;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return ParsedFeedItemAuthor
     */
    public function getAuthor(): ?ParsedFeedItemAuthor
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor(?ParsedFeedItemAuthor $author)
    {
        $this->author = $author;
    }
}
