<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

use SocialRss\Helper\Html;

class GraffitiAttachment extends AbstractAttachment
{
    public function getAttachmentOutput(): string
    {
        return 'Граффити: ' . Html::img($this->attachment['graffiti']['photo_604']);
    }
}
