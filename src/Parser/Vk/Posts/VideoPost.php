<?php


namespace SocialRss\Parser\Vk\Posts;

use SocialRss\Parser\Vk\VkParserTrait;

/**
 * Class VideoPost
 * @package SocialRss\Parser\Vk\Posts
 */
class VideoPost extends AbstractPost implements PostInterface
{
    use VkParserTrait;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getUserName() . ': новые видеозаписи';
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return self::URL . "videos{$this->users[$this->item['source_id']]['id']}";
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        $videos = $this->item['video'];

        $videos = array_filter($videos, function ($video) {
            return isset($video['title']);
        });

        $videos = array_map(function ($video) {
            return $this->makeVideoTrait($video);
        }, $videos);

        return implode(PHP_EOL, $videos);
    }
}
