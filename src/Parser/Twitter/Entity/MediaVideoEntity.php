<?php
declare(strict_types=1);


namespace SocialRss\Parser\Twitter\Entity;

use SocialRss\Helper\Html;

/**
 * Class MediaVideoEntity
 * @package SocialRss\Parser\Twitter\Entity
 */
class MediaVideoEntity extends AbstractEntity
{
    /**
     * @param array $item
     * @return bool
     */
    public static function isApplicable(array $item): bool
    {
        return in_array(static::getEntityType($item), [
            'media_video',
            'media_animated_gif'
        ], true);
    }

    /**
     * @return string
     */
    public function getParsedContent(): string
    {
        $videoVariants = array_filter($this->item['video_info']['variants'], function ($variant) {
            return $variant['content_type'] === 'video/mp4';
        });

        if (empty($videoVariants)) {
            $media = Html::img($this->item['media_url_https']);
        } else {
            // first element in $videoVariants array
            $media = Html::video(reset($videoVariants)['url'], $this->item['media_url_https']);
        }

        return $this->replaceContent($this->text, $this->item['url'], '') . PHP_EOL . $media;
    }
}
