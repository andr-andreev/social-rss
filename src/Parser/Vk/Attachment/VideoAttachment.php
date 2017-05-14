<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;


use SocialRss\Parser\Vk\Helper;

/**
 * Class VideoAttachment
 * @package SocialRss\Parser\Vk\Attachment
 */
class VideoAttachment extends AbstractAttachment implements AttachmentInterface
{

    /**
     * @return string
     */
    public function getAttachmentOutput(): string
    {
        return Helper::makeVideoTrait($this->attachment['video']);
    }
}