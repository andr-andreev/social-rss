<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

use SocialRss\Helper\Html;

class PhotoAttachment extends AbstractAttachment
{
    protected const SIZES = [
        'photo_75',
        'photo_130',
        'photo_604',
        'photo_807',
        'photo_1280',
        // 'photo_2560',
    ];

    public function getAttachmentOutput(): string
    {
        $photo = '';
        foreach (array_reverse(self::SIZES) as $size) {
            if (isset($this->attachment['photo'][$size])) {
                $photo = $this->attachment['photo'][$size];
                break;
            }
        }

        if (!$photo) {
            return '';
        }

        return Html::img($photo);
    }
}
