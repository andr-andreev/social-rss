<?php

namespace SocialRSS;


class FeedTest extends \PHPUnit_Framework_TestCase
{

    protected $feed;

    public function testSetChannelTitle()
    {
        $this->feed->setChannelTitle('title');
        $this->assertContains('title', $this->feed->getChannelElements());
    }

    public function testSetChannelLink()
    {
        $this->feed->setChannelLink('link');
        $this->assertContains('link', $this->feed->getChannelElements());
    }

    public function testGetChannelElements()
    {
        $this->feed->setChannelTitle('title');
        $this->feed->setChannelLink('link');
        $this->assertCount(2, $this->feed->getChannelElements());
    }

    public function testAddItem()
    {
        $this->feed->addItem(new Item);
        $this->feed->addItem(new Item);
        $this->feed->addItem(new Item);
        $this->assertSame(3, $this->feed->getItemsCount());
    }

    public function testMakeElement()
    {
        $this->assertNotContains('CDATA', $this->feed->makeElement('element', 'content'));
        $this->assertContains('CDATA', $this->feed->makeElement('element', '<b>content</b>'));
        $this->assertContains('CDATA', $this->feed->makeElement('element', 'content & content'));
        $this->assertNull($this->feed->makeElement('element', ''));
        $this->assertContains('element', $this->feed->makeElement('element', 'content'));
        $this->assertContains('content', $this->feed->makeElement('element', 'content'));

    }

    public function testGetItem()
    {
        $item = new Item;
        $this->feed->addItem($item);
        $this->feed->addItem(new Item);

        $this->assertSame($item, $this->feed->getItem(0));
        $this->assertNotSame($item, $this->feed->getItem(1));
        $this->assertNull($this->feed->getItem(2));
    }

    public function testMakeFeed()
    {
        $item1 = new Item;
        $item1->setTitle('title');
        $this->feed->addItem($item1);

        $item2 = new Item;
        $item2->setDescription('description');
        $this->feed->addItem($item2);

        $this->assertContains('title', $this->feed->makeFeed());
        $this->assertContains('description', $this->feed->makeFeed());
    }

    protected function setUp()
    {
        $this->feed = new Feed;
    }

}