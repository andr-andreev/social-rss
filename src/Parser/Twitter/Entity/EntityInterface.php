<?php
declare(strict_types=1);


namespace SocialRss\Parser\Twitter\Entity;

interface EntityInterface
{
    public static function isApplicable(array $item): bool;

    public function getParsedContent(): string;
}
