<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

use SocialRss\Helper\Html;

class DocumentAttachment extends AbstractAttachment
{
    public function getAttachmentOutput(): string
    {
        $preview = '';

        if (isset($this->attachment['doc']['preview']['photo']['sizes'][0]['src'])) {
            $previewSrc = $this->attachment['doc']['preview']['photo']['sizes'][0]['src'];
            $preview = Html::img($previewSrc);
        }

        return 'Документ: ' .
            Html::link($this->attachment['doc']['url'], trim($this->attachment['doc']['title'] . PHP_EOL . $preview));
    }
}
