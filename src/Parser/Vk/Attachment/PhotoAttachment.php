<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

use SocialRss\Helper\Html;

/**
 * Class PhotoAttachment
 * @package SocialRss\Parser\Vk\Attachment
 */
class PhotoAttachment extends AbstractAttachment
{

    /**
     * @return string
     */
    public function getAttachmentOutput(): string
    {
        return Html::img($this->attachment['photo']['src_big']);
    }
}
