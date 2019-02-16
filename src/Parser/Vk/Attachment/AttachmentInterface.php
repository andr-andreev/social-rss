<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

interface AttachmentInterface
{
    public function __construct(array $attachment);

    public function getAttachmentOutput(): string;
}
