<?php
namespace SocialRSS;

class Item
{
    private $item = [];

    public function getElements()
    {
        return $this->item;
    }

    public function setTitle($title)
    {
        $this->setElement('title', $title);
    }

    private function setElement($element, $content)
    {
        $this->item[$element] = $content;
    }

    public function setLink($link)
    {
        $this->setElement('link', $link);
        $this->setElement('guid', $link);
    }

    public function setDescription($description)
    {
        $this->setElement('description', $description);
    }

    public function setAuthor($author)
    {
        $this->setElement('author', $author);
    }

    public function setDate($date)
    {
        $this->setElement('pubDate', date("r", $date));
    }

}