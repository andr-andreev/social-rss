<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

class PhotosListAttachment extends AbstractAttachment
{
    public function getAttachmentOutput(): string
    {
        return '[Список фотографий]';
    }
}
