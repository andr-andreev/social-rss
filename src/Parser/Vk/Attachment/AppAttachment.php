<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

class AppAttachment extends AbstractAttachment
{
    public function getAttachmentOutput(): string
    {
        return "Приложение: {$this->attachment['app']['name']}";
    }
}
