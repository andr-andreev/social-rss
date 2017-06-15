<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\Parser\Vk\Helper;
use SocialRss\Parser\Vk\VkParser;

/**
 * Class PhotoPost
 *
 * @package SocialRss\Parser\Vk\Post
 */
class PhotoPost extends AbstractPost
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getUserName() . ': новые фотографии';
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return VkParser::getUrl() . $this->getUser()->getScreenName();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {

        return Helper::makePhotos($this->item['photos']);
    }
}
