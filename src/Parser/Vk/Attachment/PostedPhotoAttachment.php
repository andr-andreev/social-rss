<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

use SocialRss\Helper\Html;

/**
 * Class PostedPhotoAttachment
 * @package SocialRss\Parser\Vk\Attachment
 */
class PostedPhotoAttachment extends AbstractAttachment implements AttachmentInterface
{

    /**
     * @return string
     */
    public function getAttachmentOutput(): string
    {
        return Html::img($this->attachment['posted_photo']['photo_604']);
    }
}
