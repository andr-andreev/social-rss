<?php
declare(strict_types=1);


namespace SocialRss\Parser\Twitter\Entity;

/**
 * Interface EntityInterface
 * @package SocialRss\Parser\Twitter\Entity
 */
/**
 * Interface EntityInterface
 * @package SocialRss\Parser\Twitter\Entity
 */
interface EntityInterface
{

    /**
     * @param array $item
     * @return bool
     */
    public static function isApplicable(array $item): bool;

    /**
     * @return string
     */
    public function getParsedContent(): string;
}
