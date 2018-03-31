<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

use SocialRss\Helper\Html;

/**
 * Class LinkAttachment
 * @package SocialRss\Parser\Vk\Attachment
 */
class LinkAttachment extends AbstractAttachment
{

    /**
     * @return string
     */
    public function getAttachmentOutput(): string
    {
        $linkUrl = $this->attachment['link']['url'];
        $linkTitle = $this->attachment['link']['title'];

        $link = Html::link($linkUrl, $linkTitle);

        $description = $this->attachment['link']['description'];

        if (isset($this->attachment['link']['photo']['photo_604'])) {
            $preview = $this->attachment['link']['photo']['photo_604'];

            $description = Html::img($preview, $linkUrl) . PHP_EOL . $description;
        }

        return PHP_EOL . 'Ссылка: ' . $link . PHP_EOL . $description;
    }
}
