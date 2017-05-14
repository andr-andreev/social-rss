<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Post;


/**
 * Class CopyPost
 *
 * @package SocialRss\Parser\Vk\Post
 */
class CopyPost extends PostPost implements PostInterface
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getUserName() . ' (репост)';
    }

}
