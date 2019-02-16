<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

class AudioAttachment extends AbstractAttachment
{
    public function getAttachmentOutput(): string
    {
        return 'Аудиозапись: ' .
            "{$this->attachment['audio']['artist']} &ndash; {$this->attachment['audio']['title']}";
    }
}
