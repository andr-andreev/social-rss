<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

/**
 * Class PhotosListAttachment
 * @package SocialRss\Parser\Vk\Attachment
 */
class PhotosListAttachment extends AbstractAttachment implements AttachmentInterface
{

    /**
     * @return string
     */
    public function getAttachmentOutput(): string
    {
        return '[Список фотографий]';
    }
}
