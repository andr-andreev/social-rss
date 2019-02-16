<?php
declare(strict_types=1);


namespace SocialRss\Parser\Twitter\Entity;

abstract class AbstractEntity implements EntityInterface
{
    /** @var array */
    protected $item;

    /** @var string */
    protected $text;

    /** @var array */
    protected static $applicableTypes = [];

    public function __construct(array $item, string $text)
    {
        $this->item = $item;
        $this->text = $text;
    }

    public static function getEntityType(array $item): string
    {
        return $item['entity_type'];
    }

    protected function replaceContent(string $text, string $search, string $replace): string
    {
        $quotedSearch = preg_quote($search, '/');
        // replace text except already replaced inside HTML tags
        return preg_replace("/({$quotedSearch})(?=[^>]*(<|$))/i", $replace, $text, 1);
    }
}
