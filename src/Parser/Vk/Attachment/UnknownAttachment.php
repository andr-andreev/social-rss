<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

class UnknownAttachment extends AbstractAttachment
{
    public function getAttachmentOutput(): string
    {
        return '[Item contains unknown attachment type]';
    }
}
