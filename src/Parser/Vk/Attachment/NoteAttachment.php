<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

use SocialRss\Helper\Html;

/**
 * Class NoteAttachment
 * @package SocialRss\Parser\Vk\Attachment
 */
class NoteAttachment extends AbstractAttachment implements AttachmentInterface
{

    /**
     * @return string
     */
    public function getAttachmentOutput(): string
    {
        $noteLink = $this->attachment['note']['view_url'];
        $noteTitle = $this->attachment['note']['title'];

        return 'Заметка: ' . Html::link($noteLink, $noteTitle);
    }
}
