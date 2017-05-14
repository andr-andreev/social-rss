<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;


/**
 * Interface AttachmentInterface
 * @package SocialRss\Parser\Vk\Attachment
 */
interface AttachmentInterface
{
    /**
     * AttachmentInterface constructor.
     * @param array $attachment
     */
    public function __construct(array $attachment);

    /**
     * @return string
     */
    public function getAttachmentOutput(): string;
}