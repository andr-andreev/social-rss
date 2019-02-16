<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

use SocialRss\Parser\Vk\Helper;

class VideoAttachment extends AbstractAttachment
{
    public function getAttachmentOutput(): string
    {
        return Helper::makeVideoTrait($this->attachment['video']);
    }
}
