<?php


namespace SocialRss\Parser\Vk\Posts;

/**
 * Class PhotoPost
 * @package SocialRss\Parser\Vk\Posts
 */
class PhotoPost extends AbstractPost implements PostInterface
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getUserName() . ': новые фотографии';
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return self::URL . $this->users[$this->item['source_id']]['screen_name'];
    }

    /**
     * @return string
     */
    public function getDescription()
    {

        return $this->makePhotos($this->item['photos']);
    }
}
