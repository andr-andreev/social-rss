<?php


namespace SocialRss\Parser\Vk\Posts;

/**
 * Class PhotoTagPost
 * @package SocialRss\Parser\Vk\Posts
 */
class PhotoTagPost extends AbstractPost implements PostInterface
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getUserName() . ': новые отметки';
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

        return $this->makePhotos($this->item['photo_tags']);
    }
}
