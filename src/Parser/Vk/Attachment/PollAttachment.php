<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

/**
 * Class PollAttachment
 * @package SocialRss\Parser\Vk\Attachment
 */
class PollAttachment extends AbstractAttachment
{

    /**
     * @return string
     */
    public function getAttachmentOutput(): string
    {
        return "Опрос: {$this->attachment['poll']['question']}";
    }
}
