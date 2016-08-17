<?php


namespace SocialRss\Parser\Vk\Posts;

class PhotoPost extends AbstractPost implements PostInterface
{
    public function getTitle()
    {
        return $this->getUserName() . ': новые фотографии';
    }

    public function getLink()
    {
        return self::URL . $this->users[$this->item['source_id']]['screen_name'];
    }

    public function getDescription()
    {

        return $this->makePhotos($this->item['photos']);
    }
}
