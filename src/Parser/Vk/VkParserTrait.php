<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk;

/**
 * Class VkParserTrait
 * @package SocialRss\Parser\Vk
 */
trait VkParserTrait
{
    /**
     * @param $text
     * @return string
     */
    protected function parseContent(string $text): string
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
                $text = str_replace($match, $this->makeLink(self::URL . $userId, $tag), $text);
            }
        }

        // Match #hashtags
        $text = $this->parseByPattern(
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
    protected function makePhotos(array $items): string
    {
        $photos = array_filter($items, function ($photo) {
            return isset($photo['pid']);
        });

        $photos = array_map(function ($photo) {
            return $this->makeImg(
                $photo['src_big'],
                self::URL . "photo{$photo['owner_id']}_{$photo['pid']}"
            );
        }, $photos);

        return implode(PHP_EOL, $photos);
    }

    /**
     * @param $userId
     * @return string
     */
    protected function makeFriends(int $userId): string
    {
        return $this->makeLink(
            self::URL . $this->users[$userId]['screen_name'],
            $this->users[$userId]['name'] . PHP_EOL . $this->makeImg($this->users[$userId]['photo'])
        );
    }

    /**
     * @param $attachment
     * @return string
     */
    protected function makeVideoTrait(array $attachment): string
    {
        $videoLink = self::URL . "video{$attachment['owner_id']}_{$attachment['vid']}";
        $videoTitle = $attachment['title'];

        $imagePreview = $this->makeImg($attachment['image']);
        $linkToVideo = $this->makeLink($videoLink, $videoTitle);
        $videoDuration = gmdate('H:i:s', $attachment['duration']);

        return $imagePreview . PHP_EOL .
            "Видеозапись: $linkToVideo ($videoDuration)";
    }
}
