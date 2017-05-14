<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk;

use SocialRss\Helper\Html;

/**
 * Class VkHelper
 *
 * @package SocialRss\Parser\Vk
 */
class Helper
{
    /**
     * @param $text
     * @return string
     */
    public static function parseContent(string $text): string
    {
        // Match URLs
        $text = preg_replace(
            '!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i',
            '<a href="$1">$1</a>',
            $text
        );

        // Match user tags [id1|User]
        preg_match_all('/\\[(.*?)\\]/', $text, $matches);
        foreach ($matches[0] as $key => $match) {
            $list = explode('|', $matches[1][$key]);
            if (count($list) == 2) {
                list($userId, $tag) = $list;
                $text = str_replace($match, Html::link(VkParser::getUrl() . $userId, $tag), $text);
            }
        }

        // Match #hashtags
        $text = Html::parseByPattern(
            '#',
            '<a href="https://vk.com/feed?section=search&q=%23{{string}}">#{{string}}</a>',
            $text
        );

        return $text;
    }

    /**
     * @param $items
     * @return string
     */
    public static function makePhotos(array $items): string
    {
        $photos = array_filter($items, function ($photo) {
            return isset($photo['pid']);
        });

        $photos = array_map(function ($photo) {
            return Html::img(
                $photo['src_big'],
                VkParser::getUrl() . "photo{$photo['owner_id']}_{$photo['pid']}"
            );
        }, $photos);

        return implode(PHP_EOL, $photos);
    }

    /**
     * @param $attachment
     * @return string
     */
    public static function makeVideoTrait(array $attachment): string
    {
        $videoLink = VkParser::getUrl() . "video{$attachment['owner_id']}_{$attachment['vid']}";
        $videoTitle = $attachment['title'];

        $imagePreview = Html::img($attachment['image']);
        $linkToVideo = Html::link($videoLink, $videoTitle);
        $videoDuration = gmdate('H:i:s', $attachment['duration']);

        return $imagePreview . PHP_EOL .
            "Видеозапись: $linkToVideo ($videoDuration)";
    }
}
