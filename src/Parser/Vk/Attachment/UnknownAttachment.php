<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;


/**
 * Class UnknownAttachment
 * @package SocialRss\Parser\Vk\Attachment
 */
class UnknownAttachment extends AbstractAttachment implements AttachmentInterface
{

    /**
     * @return string
     */
    public function getAttachmentOutput(): string
    {
        return '[Item contains unknown attachment type]';
    }
}