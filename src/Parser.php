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
            if ($item instanceof Item) {
                $this->feed->addItem($item);
            }
        }
    }

    abstract protected function generateItem($item);

    protected function makeBlock($avatar, $content)
    {
        return "<div style='display: flex;'><div style='flex: 1; max-width: 50px; margin-right: 10px;'>{$avatar}</div><div style='flex: 1;'>{$content}</div></div>";
    }

    protected function makeImg($img, $link = null)
    {
        $middlePart = "<img src='{$img}' />";

        return empty($link) ? $middlePart : $this->makeLink($link, $middlePart);
    }

    protected function makeVideo($video, $img = '')
    {
        return "<video src='$video' poster='$img' controls></video>";
    }

    protected function makeLink($href, $text)
    {
        return "<a href='{$href}'>$text</a>";
    }
}
