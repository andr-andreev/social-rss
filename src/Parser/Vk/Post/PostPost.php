<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\Data\PostData;
use SocialRss\Parser\Vk\Helper;
use SocialRss\Parser\Vk\VkParser;

class PostPost extends AbstractVkPost
{
    public function getTitle(): string
    {
        return $this->getUserName();
    }

    public function getLink(): string
    {
        $postId = $this->item['post_id'] ?? $this->item['id'];

        return VkParser::getUrl() . "wall{$this->getUser()->getId()}_{$postId}";
    }

    public function getDescription(): string
    {
        return Helper::parseContent($this->item['text']);
    }

    public function getQuote(): ?PostData
    {
        if (!isset($this->item['copy_history'][0])) {
            return parent::getQuote();
        }

        $content = isset($this->item['copy_history'][0]['text'])
            ? Helper::parseContent($this->item['copy_history'][0]['text'])
            : '';
        $copyOwner = $this->users->getUserById($this->item['copy_history'][0]['owner_id']);
        $link = VkParser::getUrl()
            . "wall{$this->item['copy_history'][0]['from_id']}_{$this->item['copy_history'][0]['id']}";

        return new PostData([
            'title' => $copyOwner->getName(),
            'link' => $link,
            'content' => $content,
        ]);
    }
}
