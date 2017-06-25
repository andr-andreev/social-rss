<?php
declare(strict_types=1);


namespace SocialRss\Parser\Twitter\Entity;

use SocialRss\Helper\Html;
use SocialRss\Parser\Twitter\TwitterParser;

/**
 * Class SymbolsEntity
 * @package SocialRss\Parser\Twitter\Entity
 */
class SymbolsEntity extends AbstractEntity
{
    /**
     * @param array $item
     * @return bool
     */
    public static function isApplicable(array $item): bool
    {
        return static::getEntityType($item) === 'symbols';
    }

    /**
     * @return string
     */
    public function getParsedContent(): string
    {
        return $this->replaceContent(
            $this->text,
            '$' . $this->item['text'],
            Html::link(TwitterParser::getUrl() . "search?q=%24{$this->item['text']}", '$' . $this->item['text'])
        );
    }
}
