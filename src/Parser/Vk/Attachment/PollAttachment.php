<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;


/**
 * Class PollAttachment
 * @package SocialRss\Parser\Vk\Attachment
 */
class PollAttachment extends AbstractAttachment implements AttachmentInterface
{

    /**
     * @return string
     */
    public function getAttachmentOutput(): string
    {
//        $answers = array_map(function ($answer) {
        //            return $answer['text'];
        //        }, $this->attachment['poll']['answers']);

        return "Опрос: {$this->attachment['poll']['question']}";
    }
}