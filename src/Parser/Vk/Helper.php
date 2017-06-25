<?php
declare(strict_types=1);


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
        $methodsList = [
            'makeLinkableUrls',
            'makeLinkableUserMentions',
            'makeLinkableHashtags'
        ];

        return array_reduce($methodsList, function ($acc, $function) {
            return call_user_func([Helper::class, $function], $acc);
        }, $text);
    }

    /**
     * Match URLs
     *
     * @param string $text
     * @return string
     */
    public static function makeLinkableUrls(string $text): string
    {
        return preg_replace(
            '!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i',
            '<a href="$1">$1</a>',
            $text
        );
    }

    /**
     * Match user tags [id1|User]
     *
     * @param string $text
     * @return string
     */
    public static function makeLinkableUserMentions(string $text): string
    {
        $out = $text;

        preg_match_all('/\\[(.*?)\\]/', $text, $matches);
        foreach ($matches[0] as $key => $match) {
            $list = explode('|', $matches[1][$key]);
            if (count($list) === 2) {
                list($userId, $tag) = $list;
                $out = str_replace($match, Html::link(VkParser::getUrl() . $userId, $tag), $text);
            }
        }

        return $out;
    }

    /**
     * Match #hashtags
     *
     * @param string $text
     * @return string
     */
    public static function makeLinkableHashtags(string $text): string
    {
        return Html::parseByPattern(
            '#',
            '<a href="https://vk.com/feed?section=search&q=%23{{string}}">#{{string}}</a>',
            $text
        );
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
