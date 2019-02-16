<?php
declare(strict_types=1);


namespace SocialRss\Parser\Twitter\Entity;

use SocialRss\Helper\Html;
use SocialRss\Parser\Twitter\TwitterParser;

class UserMentionEntity extends AbstractEntity
{
    public static function isApplicable(array $item): bool
    {
        return static::getEntityType($item) === 'user_mentions';
    }

    public function getParsedContent(): string
    {
        $userName = $this->item['screen_name'];
        $quotedUserName = preg_quote($userName, '/');
        $pattern = '/@' . $quotedUserName . '\b/mi';

        return preg_replace_callback($pattern, function ($matches) use ($userName) {
            $href = TwitterParser::getUrl() . $userName;
            $text = $matches[0];

            return Html::link($href, $text);
        }, $this->text);
    }
}
