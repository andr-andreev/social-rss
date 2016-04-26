<?php
namespace SocialRSS;


class ItemTest extends \PHPUnit_Framework_TestCase
{
    protected $item;

    public function testGetElements()
    {
        $this->item->setTitle('title');
        $this->item->setDescription('description');
        $this->item->setAuthor('author');
        $this->assertCount(3, $this->item->getElements());
    }

    public function testSetTitle()
    {
        $this->item->setTitle('title');
        $this->assertContains('title', $this->item->getElements());
    }

    public function testSetLink()
    {
        $this->item->setLink('link');
        $this->assertContains('link', $this->item->getElements());
    }

    public function testSetDescription()
    {
        $this->item->setDescription('description');
        $this->assertContains('description', $this->item->getElements());
    }

    public function testSetAuthor()
    {
        $this->item->setAuthor('author');
        $this->assertContains('author', $this->item->getElements());
    }

    public function testSetDate()
    {
        $expected = time();
        $this->item->setDate($expected);
        $this->assertContains(date("r", $expected), $this->item->getElements());
    }

    protected function setUp()
    {
        $this->item = new Item;
    }
}
