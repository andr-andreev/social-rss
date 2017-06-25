<?php
declare(strict_types=1);


namespace SocialRss\Parser\Twitter\Entity;

use SocialRss\Helper\Html;
use SocialRss\Parser\Twitter\TwitterParser;

/**
 * Class UserMentionsEntity
 * @package SocialRss\Parser\Twitter\Entity
 */
class UserMentionsEntity extends AbstractEntity
{
    /**
     * @param array $item
     * @return bool
     */
    public static function isApplicable(array $item): bool
    {
        return static::getEntityType($item) === 'user_mentions';
    }

    /**
     * @return string
     */
    public function getParsedContent(): string
    {
        return $this->replaceContent(
            $this->text,
            "@{$this->item['screen_name']}",
            Html::link(TwitterParser::getUrl() . $this->item['screen_name'], "@{$this->item['screen_name']}")
        );
    }
}
