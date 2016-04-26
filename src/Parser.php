<?php

namespace SocialRSS;

abstract class Parser
{
    protected $socialFeed;
    protected $feed;

    public function __construct(Feed $feed)
    {
        $this->feed = $feed;
    }

    public function generateItems()
    {
        foreach ($this->socialFeed as $feedItem) {
            $item = $this->generateItem($feedItem);
            if (!is_null($item)) {
                $this->feed->addItem($item);
            }
        }
    }

    abstract protected function generateItem($item);

    protected function makeBlock($left, $right)
    {
        return "<div style='overflow: hidden'><div style='float: left; margin-right: 5px'>{$left}</div><div style='float: left'>{$right}</div></div>";
    }

    protected function makeImg($img, $link = null)
    {
        $middlePart = "<img src='{$img}' />";

        return empty($link) ? $middlePart : $this->makeLink($link, $middlePart);
    }

    protected function makeLink($href, $text)
    {
        return "<a href='{$href}'>$text</a>";
    }
}
