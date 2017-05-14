<?php
declare(strict_types=1);


namespace SocialRss\Parser\Twitter\Entity;


/**
 * Class UnknownEntity
 * @package SocialRss\Parser\Twitter\Entity
 */
class UnknownEntity extends AbstractEntity implements EntityInterface
{
    /**
     * @return string
     */
    public function getParsedContent()
    {
        return $this->text . PHP_EOL . "[Tweet contains unknown entity type {$this->item['type']}]";
    }
}