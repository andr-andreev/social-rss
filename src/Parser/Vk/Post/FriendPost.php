<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\Helper\Html;
use SocialRss\Parser\Vk\VkParser;

/**
 * Class FriendPost
 *
 * @package SocialRss\Parser\Vk\Post
 */
class FriendPost extends AbstractPost
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getUserName() . ': новые друзья';
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return VkParser::getUrl() . "friends?id={$this->getUser()->getId()}";
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        if (!isset($this->item['friends'])) {
            return '';
        }

        $users = $this->users;

        $friends = $this->item['friends']['items'];

        $friends = array_filter($friends, function ($friend) {
            return $friend['user_id'];
        });

        $friends = array_map(function ($friend) use ($users) {
            $user = $users->getUserById($friend['user_id']);

            return Html::link(
                VkParser::getUrl() . $user->getScreenName(),
                $user->getName() . PHP_EOL . Html::img($user->getPhotoUrl())
            );
        }, $friends);

        return implode(PHP_EOL, $friends);
    }
}
