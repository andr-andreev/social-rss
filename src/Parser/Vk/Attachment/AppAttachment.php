<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

/**
 * Class AppAttachment
 * @package SocialRss\Parser\Vk\Attachment
 */
class AppAttachment extends AbstractAttachment
{

    /**
     * @return string
     */
    public function getAttachmentOutput(): string
    {
        return "Приложение: {$this->attachment['app']['name']}";
    }
}
