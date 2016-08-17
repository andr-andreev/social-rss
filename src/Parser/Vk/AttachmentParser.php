<?php


namespace SocialRss\Parser\Vk;

use SocialRss\Parser\ParserTrait;

class AttachmentParser
{
    use ParserTrait;
    use VkParserTrait;
    const URL = 'https://vk.com/';

    private $item;

    private $attachmentsMap = [
        'photo' => 'makePhoto',
        'posted_photo' => 'makePostedPhoto',
        'video' => 'makeVideoAttachment',
        'audio' => 'makeAudio',
        'doc' => 'makeDoc',
        'graffiti' => 'makeGraffiti',
        'link' => 'makeLinkAttach',
        'note' => 'makeNote',
        'app' => 'makeApp',
        'poll' => 'makePoll',
        'page' => 'makePage',
        'album' => 'makeAlbum',
        'photos_list' => 'makePhotosList',
    ];

    public function __construct($item)
    {
        $this->item = $item;
    }

    public function parseAttachments()
    {
        if (!isset($this->item['attachments'])) {
            return '';
        }

        $map = $this->attachmentsMap;

        $attachments = array_map(function ($attachment) use ($map) {
            $type = $attachment['type'];

            if (!isset($map[$type])) {
                return "[Item contains unknown attachment type {$attachment['type']}]";
            }

            $method = $map[$type];
            return $this->$method($attachment);
        }, $this->item['attachments']);

        return implode(PHP_EOL, $attachments);
    }

    private function makePhoto($attachment)
    {
        return $this->makeImg($attachment['photo']['src_big']);
    }

    private function makePostedPhoto($attachment)
    {
        return $this->makeImg($attachment['posted_photo']['photo_604']);
    }

    private function makeVideoAttachment($attachment)
    {
        return $this->makeVideoTrait($attachment['video']);
    }

    private function makeAudio($attachment)
    {
        return "Аудиозапись: " .
        "{$attachment['audio']['artist']} &ndash; {$attachment['audio']['title']}";
    }

    private function makeDoc($attachment)
    {
        return 'Документ: ' .
        $this->makeLink($attachment['doc']['url'], $attachment['doc']['title']);
    }

    private function makeGraffiti($attachment)
    {
        return 'Граффити: ' . $this->makeImg($attachment['graffiti']['photo_604']);
    }

    private function makeLinkAttach($attachment)
    {
        $link = PHP_EOL . 'Ссылка: ' . $this->makeLink(
                $attachment['link']['url'],
                $attachment['link']['title']
            );

        $description = $attachment['link']['description'];

        if (isset($attachment['link']['image_src'])) {
            $description = $this->makeBlock($this->makeImg(
                $attachment['link']['image_src'],
                $attachment['link']['url']
            ), $attachment['link']['description']);
        }

        return $link . PHP_EOL . $description;
    }

    private function makeNote($attachment)
    {
        $noteLink = $attachment['note']['view_url'];
        $noteTitle = $attachment['note']['title'];

        return 'Заметка: ' . $this->makeLink($noteLink, $noteTitle);
    }

    private function makeApp($attachment)
    {
        return "Приложение: {$attachment['app']['name']}";
    }

    private function makePoll($attachment)
    {
//        $answers = array_map(function ($answer) {
//            return $answer['text'];
//        }, $attachment['poll']['answers']);

        return "Опрос: {$attachment['poll']['question']}";
    }

    private function makePage($attachment)
    {
        $pageLink = $attachment['page']['view_url'];
        $pageTitle = $attachment['page']['title'];

        return 'Страница: ' . $this->makeLink($pageLink, $pageTitle);
    }

    private function makeAlbum($attachment)
    {
        $albumTitle = $attachment['album']['title'];
        $albumSize = $attachment['album']['size'];

        return "Альбом: $albumTitle ($albumSize фото)";
    }

    private function makePhotosList($attachment)
    {
        return '[Список фотографий]';
    }
}
