<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\Parser\Vk\Helper;
use SocialRss\Parser\Vk\VkParser;

class PhotoTagPost extends AbstractVkPost
{
    public function getTitle(): string
    {
        return $this->getUserName() . ': новые отметки';
    }

    public function getLink(): string
    {
        return VkParser::getUrl() . $this->getUser()->getScreenName();
    }

    public function getDescription(): string
    {
        return Helper::makePhotos($this->item['photo_tags']);
    }
}
