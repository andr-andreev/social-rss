<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\ParsedFeed\ParsedFeedItem;
use SocialRss\Parser\Vk\Helper;
use SocialRss\Parser\Vk\VkParser;

/**
 * Class PostPost
 *
 * @package SocialRss\Parser\Vk\Post
 */
class PostPost extends AbstractPost
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

    /**
     * @return array|null|ParsedFeedItem
     */
    public function getQuote(): ?ParsedFeedItem
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

        return new ParsedFeedItem($copyOwner->getName(), $link, $content);
    }
}
