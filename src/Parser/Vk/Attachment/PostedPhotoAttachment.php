<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

use SocialRss\Helper\Html;

class PostedPhotoAttachment extends AbstractAttachment
{
    public function getAttachmentOutput(): string
    {
        return Html::img($this->attachment['posted_photo']['photo_604']);
    }
}
