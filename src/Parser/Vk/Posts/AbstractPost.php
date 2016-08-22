<?php


namespace SocialRss\Parser\Vk\Posts;

use SocialRss\Parser\ParserTrait;
use SocialRss\Parser\Vk\VkParserTrait;

/**
 * Class AbstractPost
 * @package SocialRss\Parser\Vk\Posts
 */
class AbstractPost
{
    use ParserTrait;
    use VkParserTrait;

    const URL = 'https://vk.com/';

    protected $item;
    public $users;

    /**
     * AbstractPost constructor.
     * @param $item
     * @param $users
     */
    public function __construct($item, $users)
    {
        $this->item = $item;
        $this->users = $users;
    }

    /**
     * @return array
     */
    public function getQuote()
    {
        return [];
    }

    /**
     * @return mixed
     */
    protected function getUserName()
    {
        return $this->users[$this->item['source_id']]['name'];
    }
}
