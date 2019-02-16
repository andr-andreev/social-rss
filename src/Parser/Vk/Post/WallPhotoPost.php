<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Post;

use SocialRss\Helper\Html;
use SocialRss\Parser\Vk\VkParser;

class WallPhotoPost extends AbstractVkPost
{
    public function getTitle(): string
    {
        return $this->getUserName() . ': фото на стене';
    }

    public function getLink(): string
    {
        return VkParser::getUrl() . $this->getUser()->getScreenName();
    }

    public function getDescription(): string
    {
        $photos = array_map(function (array $photo) {
            return Html::img($photo['photo_604']);
        }, $this->item['photos']['items']);

        return implode(PHP_EOL, $photos);
    }
}
