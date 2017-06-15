<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

/**
 * Class AbstractAttachment
 * @package SocialRss\Parser\Vk\Attachment
 */
abstract class AbstractAttachment implements AttachmentInterface
{
    protected $attachment;

    /**
     * AbstractAttachment constructor.
     * @param array $attachment
     */
    public function __construct(array $attachment)
    {
        $this->attachment = $attachment;
    }
}
