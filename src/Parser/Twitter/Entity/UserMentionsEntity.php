<?php
declare(strict_types=1);


namespace SocialRss\Parser\Twitter\Entity;


use SocialRss\Helper\Html;
use SocialRss\Parser\Twitter\TwitterParser;

/**
 * Class UserMentionsEntity
 * @package SocialRss\Parser\Twitter\Entity
 */
class UserMentionsEntity extends AbstractEntity implements EntityInterface
{
    /**
     * @return string
     */
    public function getParsedContent()
    {
        return $this->replaceContent(
            $this->text,
            "@{$this->item['screen_name']}",
            Html::link(TwitterParser::getUrl() . $this->item['screen_name'], "@{$this->item['screen_name']}")
        );
    }
}