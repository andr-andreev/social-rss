<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\Helper\Html;
use SocialRss\Parser\Vk\VkParser;

/**
 * Class PostPost
 *
 * @package SocialRss\Parser\Vk\Post
 */
class WallPhotoPost extends AbstractPost
{
    /**
     * @return mixed
     */
    public function getTitle(): string
    {
        return $this->getUserName() . ': фото на стене';
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return VkParser::getUrl() . $this->getUser()->getScreenName();
    }

    /**
     * @return mixed
     */
    public function getDescription(): string
    {
        $photos = array_map(function (array $photo) {
            return Html::img($photo['photo_604']);
        }, $this->item['photos']['items']);

        return implode(PHP_EOL, $photos);
    }
}
