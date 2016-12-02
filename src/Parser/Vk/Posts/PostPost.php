<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk\Posts;

/**
 * Class PostPost
 *
 * @package SocialRss\Parser\Vk\Posts
 */
class PostPost extends AbstractPost implements PostInterface
{
    /**
     * @return mixed
     */
    public function getTitle(): string
    {
        return $this->getUserName();
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return self::URL . "wall{$this->users[$this->item['source_id']]['id']}_{$this->item['post_id']}";
    }

    /**
     * @return mixed
     */
    public function getDescription(): string
    {
        return $this->parseContent($this->item['text']);
    }

    /**
     * @return array
     */
    public function getQuote(): array
    {
        if (!isset($this->item['copy_owner_id'])) {
            return parent::getQuote();
        }

        if (isset($this->item['copy_text'])) {
            $content = $this->parseContent($this->item['copy_text']);
        } else {
            $content = '';
        }

        return [
            'title' => $this->users[$this->item['copy_owner_id']]['name'],
            'link' => self::URL . $this->users[$this->item['copy_owner_id']]['screen_name'],
            'content' => $content,
        ];
    }
}
