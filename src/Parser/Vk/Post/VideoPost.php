<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\Parser\Vk\Helper;
use SocialRss\Parser\Vk\VkParser;

/**
 * Class VideoPost
 *
 * @package SocialRss\Parser\Vk\Post
 */
class VideoPost extends AbstractPost implements PostInterface
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getUserName() . ': новые видеозаписи';
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return VkParser::getUrl() . "videos{$this->getUser()->getId()}";
    }

    /**
     * @return string
     */
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
