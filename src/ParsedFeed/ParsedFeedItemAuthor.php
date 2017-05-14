<?php
declare(strict_types=1);


namespace SocialRss\ParsedFeed;

/**
 * Class ParsedFeedItemAuthor
 * @package SocialRss\ParsedFeed
 */
class ParsedFeedItemAuthor
{
    public $name;
    public $avatar;
    public $link;

    /**
     * ParsedFeedItemAuthor constructor.
     * @param $name
     * @param $avatar
     * @param $link
     */
    public function __construct(string $name, string $avatar, string $link)
    {
        $this->name = $name;
        $this->avatar = $avatar;
        $this->link = $link;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }
}
