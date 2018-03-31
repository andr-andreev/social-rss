<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\ParsedFeed\ParsedFeedItem;
use SocialRss\Parser\Vk\User\User;
use SocialRss\Parser\Vk\User\UserCollection;

/**
 * Class AbstractPost
 *
 * @package SocialRss\Parser\Vk\Post
 */
abstract class AbstractPost implements PostInterface
{
    protected $item;
    public $users;

    /**
     * AbstractPost constructor.
     *
     * @param $item
     * @param $users
     */
    public function __construct(array $item, UserCollection $users)
    {
        $this->item = $item;
        $this->users = $users;
    }

    /**
     * @return array|null|ParsedFeedItem
     */
    public function getQuote(): ?ParsedFeedItem
    {
        return null;
    }

    /**
     * @return mixed
     */
    protected function getUserName(): string
    {
        return $this->getUser()->getName();
    }

    /**
     * @return User
     */
    protected function getUser(): User
    {
        $userId = $this->item['source_id'] ?? $this->item['from_id'];

        return $this->users->getUserById($userId);
    }
}
