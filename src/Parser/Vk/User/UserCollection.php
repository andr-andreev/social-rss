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
    public function addUser(User $user)
    {
        $userId = $user->getId();
        $this->users[$userId] = $user;
    }

    /**
     * @param $userId
     * @return null|User
     */
    public function getUserById($userId): ?User
    {
        $normalizedUserId = User::normalizeId($userId);

        if (!isset($this->users[$normalizedUserId])) {
            return null;
        }

        return $this->users[$normalizedUserId];
    }
}
