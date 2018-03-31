<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\User;

/**
 * Class UserCollection
 * @package SocialRss\Parser\Vk\User
 */
class UserCollection extends \ArrayObject
{
    protected $users = [];

    /**
     * @param User $user
     */
    public function addUser(User $user): void
    {
        $userId = $user->getId();
        $this->users[$userId] = $user;
    }

    /**
     * @param $userId
     * @return User
     */
    public function getUserById($userId): User
    {
        $normalizedUserId = User::normalizeId($userId);

        return $this->users[$normalizedUserId];
    }
}
