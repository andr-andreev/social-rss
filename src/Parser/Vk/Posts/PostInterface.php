<?php


namespace SocialRss\Parser\Vk\Posts;

interface PostInterface
{
    public function getTitle();

    public function getLink();

    public function getDescription();
}
