<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

use SocialRss\Helper\Html;

/**
 * Class DocumentAttachment
 * @package SocialRss\Parser\Vk\Attachment
 */
class DocumentAttachment extends AbstractAttachment
{

    /**
     * @return string
     */
    public function getAttachmentOutput(): string
    {
        return 'Документ: ' .
            Html::link($this->attachment['doc']['url'], $this->attachment['doc']['title']);
    }
}
