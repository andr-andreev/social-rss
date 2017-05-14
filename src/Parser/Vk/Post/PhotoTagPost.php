<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\Parser\Vk\Helper;
use SocialRss\Parser\Vk\VkParser;

/**
 * Class PhotoTagPost
 *
 * @package SocialRss\Parser\Vk\Post
 */
class PhotoTagPost extends AbstractPost implements PostInterface
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getUserName() . ': новые отметки';
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
        return Helper::makePhotos($this->item['photo_tags']);
    }
}
