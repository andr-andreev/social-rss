<?php
declare(strict_types=1);


namespace SocialRss\Parser\Twitter\Entity;

use SocialRss\Helper\Html;
use SocialRss\Parser\Twitter\TwitterParser;

/**
 * Class SymbolEntity
 * @package SocialRss\Parser\Twitter\Entity
 */
class SymbolEntity extends AbstractEntity
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
        $symbol = $this->item['text'];
        $quotedSymbol = preg_quote($symbol, '/');
        $pattern = '/\$' . $quotedSymbol . '\b/m';

        return preg_replace_callback($pattern, function ($matches) use ($symbol) {
            $href = TwitterParser::getUrl() . "search?q=%24{$symbol}";
            $text = $matches[0];

            return Html::link($href, $text);
        }, $this->text);
    }
}
