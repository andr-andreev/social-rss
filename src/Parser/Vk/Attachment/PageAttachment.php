<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

use SocialRss\Helper\Html;

class PageAttachment extends AbstractAttachment
{
    public function getAttachmentOutput(): string
    {
        $pageLink = $this->attachment['page']['view_url'];
        $pageTitle = $this->attachment['page']['title'];

        return 'Страница: ' . Html::link($pageLink, $pageTitle);
    }
}
