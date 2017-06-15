<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\Parser\Vk\VkParser;

/**
 * Class AudioPost
 *
 * @package SocialRss\Parser\Vk\Post
 */
class AudioPost extends AbstractPost
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getUserName() . ': новые аудиозаписи';
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return VkParser::getUrl() . "audios{$this->getUser()->getId()}";
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        $audios = $this->item['audio'];

        $audios = array_filter($audios, function ($audio) {
            return isset($audio['title']);
        });

        $audios = array_map(function ($audio) {
            return "Аудиозапись: {$audio['artist']} &ndash; {$audio['title']}";
        }, $audios);

        return implode(PHP_EOL, $audios);
    }
}
