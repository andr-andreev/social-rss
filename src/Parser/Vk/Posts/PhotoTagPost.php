<?php


namespace SocialRss\Parser\Vk\Posts;

class PhotoTagPost extends AbstractPost implements PostInterface
{
    public function getTitle()
    {
        return $this->getUserName() . ': новые отметки';
    }

    public function getLink()
    {
        return self::URL . $this->users[$this->item['source_id']]['screen_name'];
    }

    public function getDescription()
    {

        return $this->makePhotos($this->item['photo_tags']);
    }
}
