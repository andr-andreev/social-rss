<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk;

use SocialRss\Helper\Html;
use SocialRss\ParsedFeed\ParsedFeedItem;
use SocialRss\Parser\FeedItem\FeedItemInterface;
use SocialRss\Parser\Vk\User\UserCollection;

/**
 * Class VkFeedItem
 * @package SocialRss\Parser\Vk
 */
class VkFeedItem implements FeedItemInterface
{
    protected $item;

    /** @var UserCollection */
    protected $users;

    protected $postParser;

    /**
     * VkFeedItem constructor.
     * @param array $item
     */
    public function __construct(array $item)
    {
        $users = $item['profiles'];

        $this->item = $item;
        $this->users = $users;
        $this->postParser = (new PostParser($item, $users))->createParser();
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->postParser->getTitle();
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->postParser->getLink();
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->getTexts()['content'];
    }

    /**
     * @return array
     */
    protected function getTexts(): array
    {
        $content = $this->postParser->getDescription();
        $quote = $this->postParser->getQuote();

        $attachmentParser = new AttachmentParser($this->item);
        $attachments = $attachmentParser->getAttachmentsOutput();

        if ($quote) {
            // Swap user and repost contents
            $tmp = $quote->getContent();
            $quote->setContent($content);
            $content = $tmp;
        }

        $contentAddition = '';
        $quoteContentAddition = '';

        if (!empty($quote)) {
            $quoteContentAddition = $attachments;
        } else {
            $contentAddition = $attachments;
        }

        $content .= PHP_EOL . $contentAddition;
        $content = nl2br(trim($content));

        if ($quote && $quote->getContent()) {
            $quote->setContent($quote->getContent() . PHP_EOL . $quoteContentAddition);
            $quote->setContent(nl2br(trim($quote->getContent())));
        }

        return ['content' => $content, 'quote' => $quote];
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return \DateTime::createFromFormat('U', (string)$this->item['date']);
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return Html::getParsedByPattern('#{string}', $this->getContent());
    }

    /**
     * @return string
     */
    public function getAuthorName(): string
    {
        return $this->users->getUserById($this->item['source_id'])->getName();
    }

    /**
     * @return mixed
     */
    public function getAuthorAvatar()
    {
        return $this->users->getUserById($this->item['source_id'])->getPhotoUrl();
    }

    /**
     * @return string
     */
    public function getAuthorLink(): string
    {
        return VkParser::getUrl() . $this->users->getUserById($this->item['source_id'])->getScreenName();
    }

    /**
     * @return null|ParsedFeedItem
     */
    public function getQuote():?ParsedFeedItem
    {
        return $this->getTexts()['quote'];
    }
}
