<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\Attachment;

class PollAttachment extends AbstractAttachment
{
    public function getAttachmentOutput(): string
    {
        $answers = array_map(function (array $answer) {
            return "{$answer['text']} - {$answer['votes']} ({$answer['rate']}%)";
        }, $this->attachment['poll']['answers']);

        return "Вопрос: {$this->attachment['poll']['question']}" . PHP_EOL . implode(PHP_EOL, $answers);
    }
}
