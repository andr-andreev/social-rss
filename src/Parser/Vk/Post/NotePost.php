<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\Helper\Html;
use SocialRss\Parser\Vk\VkParser;

class NotePost extends AbstractVkPost
{
    public function getTitle(): string
    {
        return $this->getUserName() . ': новая заметка';
    }

    public function getLink(): string
    {
        return VkParser::getUrl() . $this->getUser()->getScreenName();
    }

    public function getDescription(): string
    {
        $notes = $this->item['notes'];

        $notes = array_map(function ($note) {
            return 'Заметка: ' . Html::link($note['view_url'], $note['title']);
        }, $notes);

        return implode(PHP_EOL, $notes);
    }
}
