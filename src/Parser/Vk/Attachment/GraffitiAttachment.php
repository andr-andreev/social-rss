<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;


use SocialRss\Helper\Html;

/**
 * Class GraffitiAttachment
 * @package SocialRss\Parser\Vk\Attachment
 */
class GraffitiAttachment extends AbstractAttachment implements AttachmentInterface
{

    /**
     * @return string
     */
    public function getAttachmentOutput(): string
    {
        return 'Граффити: ' . Html::img($this->attachment['graffiti']['photo_604']);
    }
}