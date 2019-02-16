<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk;

use SocialRss\Helper\Html;

class Helper
{
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

    public static function makeLinkableUrls(string $subject): string
    {
        return preg_replace_callback('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', function ($matches) {
            $url = $matches[0];

            return Html::link($url, $url);
        }, $subject);
    }

    public static function makeLinkableUserMentions(string $text): string
    {
        $out = $text;

        preg_match_all('/\\[(.*?)\\]/', $text, $matches);
        foreach ((array)$matches[0] as $key => $match) {
            $list = explode('|', $matches[1][$key]);
            if (count($list) === 2) {
                [$userId, $tag] = $list;
                $out = str_replace($match, Html::link(VkParser::getUrl() . $userId, $tag), $text);
            }
        }

        return $out;
    }

    public static function makeLinkableHashtags(string $text): string
    {
        return Html::parseByPattern(
            '#',
            'https://vk.com/feed?section=search&q=%23{{string}}',
            $text
        );
    }

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

    public static function makeVideoTrait(array $attachment): string
    {
        $videoLink = VkParser::getUrl() . "video{$attachment['owner_id']}_{$attachment['id']}";
        $videoTitle = $attachment['title'];

        $imagePreview = Html::img($attachment['photo_640'] ?? $attachment['photo_130']);
        $linkToVideo = Html::link($videoLink, $videoTitle);
        $videoDuration = gmdate('H:i:s', $attachment['duration']);

        return $imagePreview . PHP_EOL .
            "Видеозапись: $linkToVideo ($videoDuration)";
    }
}
