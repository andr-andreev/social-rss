<?php

namespace SocialRSS;

class Feed
{
    private $channel = [];
    private $items = [];

    public function setChannelTitle($title)
    {
        $this->setChannelElement('title', $title);
    }

    private function setChannelElement($element, $content)
    {
        $this->channel[$element] = $content;
    }

    public function setChannelLink($link)
    {
        $this->setChannelElement('link', $link);
    }

    public function addItem(Item $item)
    {
        $this->items[] = $item;
    }

    public function addItemsArray(array $items)
    {
        foreach ($items as $item) {
            $this->items[] = $item;
        }
    }

    public function setHeader()
    {
        header('Content-Type: application/rss+xml; charset=utf-8');
    }

    public function printFeed()
    {
        echo $this->makeFeed();
    }

    public function makeFeed()
    {
        $out = '<?xml version="1.0"?>' . PHP_EOL . '<rss version="2.0">' . PHP_EOL . '<channel>' . PHP_EOL;

        $out .= $this->makeElementsArray($this->getChannelElements());

        for ($i = 0; $i < $this->getItemsCount(); $i++) {
            $currentItem = $this->getItem($i)->getElements();

            $out .= PHP_EOL . '<item>' . PHP_EOL;
            $out .= $this->makeElementsArray($currentItem);
            $out .= '</item>' . PHP_EOL;
        }

        $out .= PHP_EOL . '</channel>' . PHP_EOL . '</rss>';

        return $out;
    }

    private function makeElementsArray(array $array)
    {
        $out = '';

        foreach ($array as $elementName => $elementContent) {
            $out .= $this->makeElement($elementName, $elementContent);
        }

        return $out;
    }

    public function makeElement($elementName, $elementContent)
    {
        if (!empty($elementContent)) {
            // Make CDATA section if necessary
            $contentPart = preg_match("/&|<|>/", $elementContent) ? "<![CDATA[$elementContent]]>" : $elementContent;
            return "<$elementName>$contentPart</$elementName>" . PHP_EOL;
        }
    }

    public function getChannelElements()
    {
        return $this->channel;
    }

    public function getItemsCount()
    {
        return count($this->items);
    }

    public function getItem($num)
    {
        return array_key_exists($num, $this->items) ? $this->items[$num] : null;
    }

}
