<?php
declare(strict_types=1);


namespace SocialRss\Parser\Twitter\Entity;

/**
 * Class UnknownEntity
 * @package SocialRss\Parser\Twitter\Entity
 */
class UnknownEntity extends AbstractEntity
{
    /**
     * @var array
     */
    protected static $knownTypes = [
        'hashtags',
        'user_mentions',
        'urls',
        'symbols',
        'media_photo',
        'media_video',
        'media_animated_gif',
    ];

    /**
     * @param array $item
     * @return bool
     */
    public static function isApplicable(array $item): bool
    {
        return !in_array(static::getEntityType($item), static::$knownTypes, true);
    }

    /**
     * @return string
     */
    public function getParsedContent(): string
    {
        return $this->text . PHP_EOL . "[Tweet contains unknown entity type {$this->item['type']}]";
    }
}
