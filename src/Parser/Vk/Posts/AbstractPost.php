<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk\Posts;

use SocialRss\Parser\ParserTrait;
use SocialRss\Parser\Vk\VkParserTrait;

/**
 * Class AbstractPost
 *
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
     *
     * @param $item
     * @param $users
     */
    public function __construct(array $item, array $users)
    {
        $this->item = $item;
        $this->users = $users;
    }

    /**
     * @return array
     */
    public function getQuote(): array
    {
        return [];
    }

    /**
     * @return mixed
     */
    protected function getUserName(): string
    {
        return $this->users[$this->item['source_id']]['name'];
    }
}
