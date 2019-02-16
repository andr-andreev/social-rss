<?php

declare(strict_types=1);

namespace SocialRss\Parser\Twitter\Entity;

use SocialRss\Helper\Html;
use SocialRss\Parser\Twitter\TwitterParser;

class HashtagEntity extends AbstractEntity
{
    public static function isApplicable(array $item): bool
    {
        return static::getEntityType($item) === 'hashtags';
    }

    public function getParsedContent(): string
    {
        $hashtag = $this->item['text'];
        $quotedHashtag = preg_quote($hashtag, '/');
        $pattern = '/#' . $quotedHashtag . '\b/umi';

        return preg_replace_callback($pattern, function ($matches) use ($hashtag) {
            $href = TwitterParser::getUrl() . "hashtag/{$hashtag}";
            $text = $matches[0];

            return Html::link($href, $text);
        }, $this->text);
    }
}
