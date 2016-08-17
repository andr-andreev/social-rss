<?php


namespace SocialRss\Parser\Vk\Posts;

use SocialRss\Parser\ParserTrait;
use SocialRss\Parser\Vk\VkParserTrait;

class AbstractPost
{
    use ParserTrait;
    use VkParserTrait;

    const URL = 'https://vk.com/';

    protected $item;
    public $users;

    public function __construct($item, $users)
    {
        $this->item = $item;
        $this->users = $users;
    }

    public function getQuote()
    {
        return [];
    }

    protected function getUserName()
    {
        return $this->users[$this->item['source_id']]['name'];
    }
}
