<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

use SocialRss\Helper\Html;

/**
 * Class PageAttachment
 * @package SocialRss\Parser\Vk\Attachment
 */
class PageAttachment extends AbstractAttachment implements AttachmentInterface
{

    /**
     * @return string
     */
    public function getAttachmentOutput(): string
    {
        $pageLink = $this->attachment['page']['view_url'];
        $pageTitle = $this->attachment['page']['title'];

        return 'Страница: ' . Html::link($pageLink, $pageTitle);
    }
}
