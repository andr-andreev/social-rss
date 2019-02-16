<?php
declare(strict_types=1);


namespace SocialRss\Parser\Twitter\Entity;

use SocialRss\Helper\Html;

class UrlEntity extends AbstractEntity
{
    public static function isApplicable(array $item): bool
    {
        return static::getEntityType($item) === 'urls';
    }

    public function getParsedContent(): string
    {
        return $this->replaceContent(
            $this->text,
            $this->item['url'],
            Html::link($this->item['expanded_url'], $this->item['display_url'])
        );
    }
}
