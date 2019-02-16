<?php
declare(strict_types=1);


namespace SocialRss\Parser\Client;

interface ClientInterface
{
    public function getFeed(string $username): array;
}
