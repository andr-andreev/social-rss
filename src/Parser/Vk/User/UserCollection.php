<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\User;

class UserCollection extends \ArrayObject
{
    /** @var array */
    protected $users = [];

    public function addUser(User $user): void
    {
        $userId = $user->getId();
        $this->users[$userId] = $user;
    }

    public function getUserById($userId): User
    {
        return $this->users[$userId];
    }
}
