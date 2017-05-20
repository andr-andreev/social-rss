<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\Helper\Html;
use SocialRss\Parser\Vk\VkParser;

/**
 * Class NotePost
 *
 * @package SocialRss\Parser\Vk\Post
 */
class NotePost extends AbstractPost
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getUserName() . ': новая заметка';
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return VkParser::getUrl() . "{$this->getUser()->getScreenName()}";
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        $notes = $this->item['notes'];

        $notes = array_map(function ($note) {
            return 'Заметка: ' . Html::link($note['view_url'], $note['title']);
        }, $notes);

        return implode(PHP_EOL, $notes);
    }
}
