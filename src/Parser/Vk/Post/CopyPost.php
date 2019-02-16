<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Post;

class CopyPost extends PostPost
{
    public function getTitle(): string
    {
        return parent::getTitle() . ' (репост)';
    }
}
