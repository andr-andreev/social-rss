<?php


namespace SocialRss\Parser\Vk\Posts;

use SocialRss\Parser\Vk\VkParserTrait;

class VideoPost extends AbstractPost implements PostInterface
{
    use VkParserTrait;

    public function getTitle()
    {
        return $this->getUserName() . ': новые видеозаписи';
    }

    public function getLink()
    {
        return self::URL . "videos{$this->users[$this->item['source_id']]['id']}";
    }

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
