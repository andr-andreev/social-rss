<?php


namespace SocialRss\Parser\Vk\Posts;

class PostPost extends AbstractPost implements PostInterface
{
    public function getTitle()
    {
        return $this->getUserName();
    }

    public function getLink()
    {
        return self::URL . "wall{$this->users[$this->item['source_id']]['id']}_{$this->item['post_id']}";
    }

    public function getDescription()
    {
        return $this->parseContent($this->item['text']);
    }

    public function getQuote()
    {
        $title = '';
        $link = '';
        $content = '';

        if (isset($this->item['copy_owner_id'])) {
            $title = $this->users[$this->item['copy_owner_id']]['name'];
            $link = self::URL . $this->users[$this->item['copy_owner_id']]['screen_name'];
        }

        if (isset($this->item['copy_text'])) {
            $content = $this->parseContent($this->item['copy_text']);
        }

        if (empty($link)) {
            return [];
        }

        return [
            'title' => $title,
            'link' => $link,
            'content' => $content,
        ];
    }
}
