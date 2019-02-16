<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\Data\PostData;
use SocialRss\Parser\Vk\User\User;
use SocialRss\Parser\Vk\User\UserCollection;

abstract class AbstractVkPost implements VkPostInterface
{
    protected $item;
    public $users;

    public function __construct(array $item, UserCollection $users)
    {
        $this->item = $item;
        $this->users = $users;
    }

    public function getQuote(): ?PostData
    {
        return null;
    }

    protected function getUserName(): string
    {
        return $this->getUser()->getName();
    }

    protected function getUser(): User
    {
        $userId = $this->item['source_id'] ?? $this->item['from_id'];

        return $this->users->getUserById($userId);
    }
}
