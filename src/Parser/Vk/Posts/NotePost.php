<?php


namespace SocialRss\Parser\Vk\Posts;

class NotePost extends AbstractPost implements PostInterface
{
    public function getTitle()
    {
        return $this->getUserName() . ': новая заметка';
    }

    public function getLink()
    {
        return self::URL . "{$this->users[$this->item['source_id']]['screen_name']}";
    }

    public function getDescription()
    {

        $notes = $this->item['notes'];

        $notes = array_map(function ($note) {
            return 'Заметка: ' . $this->makeLink($note['view_url'], $note['title']);
        }, $notes);

        return implode(PHP_EOL, $notes);
    }
}
