<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

abstract class AbstractAttachment implements AttachmentInterface
{
    /** @var array */
    protected $attachment;

    public function __construct(array $attachment)
    {
        $this->attachment = $attachment;
    }
}
