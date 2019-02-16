<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\Parser\Vk\VkParser;

class DummyPost extends AbstractVkPost
{
    public function getTitle(): string
    {
        return $this->getUserName() . ': unknown type ' . $this->item['type'];
    }

    public function getLink(): string
    {
        return VkParser::getUrl() . $this->getUser()->getScreenName();
    }

    public function getDescription(): string
    {
        return 'Dummy content';
    }
}
