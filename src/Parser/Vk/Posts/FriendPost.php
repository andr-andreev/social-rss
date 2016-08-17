<?php


namespace SocialRss\Parser\Vk\Posts;

class FriendPost extends AbstractPost implements PostInterface
{
    public function getTitle()
    {
        return $this->getUserName() . ': новые друзья';
    }

    public function getLink()
    {
        return self::URL . "friends?id={$this->users[$this->item['source_id']]['id']}";
    }

    public function getDescription()
    {
        if (!isset($this->item['friends'])) {
            return '';
        }

        $friends = $this->item['friends'];

        $friends = array_filter($friends, function ($friend) {
            return $friend['uid'];
        });

        $friends = array_map(function ($friend) {
            return $this->makeFriends($friend['uid']);
        }, $friends);

        return implode(PHP_EOL, $friends);
    }
}
