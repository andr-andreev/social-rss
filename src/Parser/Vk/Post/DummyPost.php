<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\Parser\Vk\VkParser;

/**
 * Class AudioPost
 *
 * @package SocialRss\Parser\Vk\Post
 */
class DummyPost extends AbstractPost implements PostInterface
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getUserName() . ': unknown type ' . $this->item['type'];
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return VkParser::getUrl() . $this->getUser()->getScreenName();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Dummy content';
    }
}
