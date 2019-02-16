<?php
declare(strict_types=1);


namespace SocialRss\Parser\Twitter\Entity;

use SocialRss\Helper\Html;

class MediaPhotoEntity extends AbstractEntity
{
    public static function isApplicable(array $item): bool
    {
        return static::getEntityType($item) === 'media_photo';
    }

    public function getParsedContent(): string
    {
        return $this->replaceContent($this->text, $this->item['url'], '') .
            PHP_EOL .
            Html::img($this->item['media_url_https'], $this->item['expanded_url']);
    }
}
