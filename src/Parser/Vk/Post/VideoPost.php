<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\Parser\Vk\Helper;
use SocialRss\Parser\Vk\VkParser;

class VideoPost extends AbstractVkPost
{
    public function getTitle(): string
    {
        return $this->getUserName() . ': новые видеозаписи';
    }

    public function getLink(): string
    {
        return VkParser::getUrl() . "videos{$this->getUser()->getId()}";
    }

    public function getDescription(): string
    {
        $videos = $this->item['video'];

        $videos = array_filter($videos, function ($video) {
            return isset($video['title']);
        });

        $videos = array_map(function ($video) {
            return Helper::makeVideoTrait($video);
        }, $videos);

        return implode(PHP_EOL, $videos);
    }
}
