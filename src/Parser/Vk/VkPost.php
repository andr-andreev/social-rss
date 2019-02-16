<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk;

use SocialRss\Data\PostData;
use SocialRss\Helper\Html;
use SocialRss\Parser\Post\PostInterface;
use SocialRss\Parser\Vk\User\User;
use SocialRss\Parser\Vk\User\UserCollection;

class VkPost implements PostInterface
{
    protected $item;

    /** @var UserCollection */
    protected $users;

    /** @var User|null */
    protected $authorUser;

    protected $postParser;

    public function __construct(array $item)
    {
        $users = $item['profiles'];

        $this->item = $item;
        $this->users = $users;
        $this->postParser = (new PostParser($item, $users))->createParser();

        $this->authorUser = $this->getAuthorUser();
    }

    public function getTitle(): string
    {
        return $this->postParser->getTitle();
    }

    public function getLink(): string
    {
        return $this->postParser->getLink();
    }

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
            $quoteAttachmentParser = new AttachmentParser($this->item['copy_history'][0]);
            $quoteAttachments = $quoteAttachmentParser->getAttachmentsOutput();

            $newQuoteContent = nl2br(trim($quote->getContent() . PHP_EOL . $quoteAttachments));
            $quote->setContent($newQuoteContent);
        }

        $geoPlace = '';
        if (isset($this->item['geo']['place']['title'])) {
            $geoPlace = 'Место: ' . $this->item['geo']['place']['title'];
        }

        $content = nl2br(trim($content . PHP_EOL . $attachments . PHP_EOL . $geoPlace));

        return ['content' => $content, 'quote' => $quote];
    }

    public function getDate(): \DateTime
    {
        return \DateTime::createFromFormat('U', (string)$this->item['date']);
    }

    public function getTags(): array
    {
        return Html::getParsedByPattern('#{string}', $this->getContent());
    }

    public function getAuthorName(): string
    {
        return $this->authorUser ? $this->authorUser->getName() : '';
    }

    /**
     * @return mixed
     */
    public function getAuthorAvatar()
    {
        return $this->authorUser ? $this->authorUser->getPhotoUrl() : '';
    }

    public function getAuthorLink(): string
    {
        return $this->authorUser ? VkParser::getUrl() . $this->authorUser->getScreenName() : '';
    }

    public function getQuote(): ?PostData
    {
        return $this->getTexts()['quote'];
    }

    protected function getAuthorUser(): ?User
    {
        $id = $this->item['source_id'] ?? $this->item['from_id'];

        return $this->users->getUserById($id);
    }
}
