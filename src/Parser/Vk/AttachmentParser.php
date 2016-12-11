<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk;

use SocialRss\Parser\ParserTrait;

/**
 * Class AttachmentParser
 *
 * @package SocialRss\Parser\Vk
 */
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

    /**
     * AttachmentParser constructor.
     *
     * @param $item
     */
    public function __construct($item)
    {
        $this->item = $item;
    }

    /**
     * @return string
     */
    public function parseAttachments()
    {
        if (!isset($this->item['attachments'])) {
            return '';
        }

        $map = $this->attachmentsMap;

        $attachments = array_map(
            function (array $attachment) use ($map) {
                $type = $attachment['type'];

                if (!isset($map[$type])) {
                    return "[Item contains unknown attachment type {$attachment['type']}]";
                }

                $method = $map[$type];
                return $this->$method($attachment);
            }, $this->item['attachments']
        );

        return implode(PHP_EOL, $attachments);
    }

    /**
     * @param $attachment
     * @return string
     */
    private function makePhoto(array $attachment): string
    {
        return $this->makeImg($attachment['photo']['src_big']);
    }

    /**
     * @param $attachment
     * @return string
     */
    private function makePostedPhoto(array $attachment): string
    {
        return $this->makeImg($attachment['posted_photo']['photo_604']);
    }

    /**
     * @param $attachment
     * @return string
     */
    private function makeVideoAttachment(array $attachment): string
    {
        return $this->makeVideoTrait($attachment['video']);
    }

    /**
     * @param $attachment
     * @return string
     */
    private function makeAudio(array $attachment): string
    {
        return "Аудиозапись: " .
        "{$attachment['audio']['artist']} &ndash; {$attachment['audio']['title']}";
    }

    /**
     * @param $attachment
     * @return string
     */
    private function makeDoc(array $attachment): string
    {
        return 'Документ: ' .
        $this->makeLink($attachment['doc']['url'], $attachment['doc']['title']);
    }

    /**
     * @param $attachment
     * @return string
     */
    private function makeGraffiti(array $attachment): string
    {
        return 'Граффити: ' . $this->makeImg($attachment['graffiti']['photo_604']);
    }

    /**
     * @param $attachment
     * @return string
     */
    private function makeLinkAttach(array $attachment): string
    {
        $linkUrl = $attachment['link']['url'];
        $linkTitle = $attachment['link']['title'];

        $link = $this->makeLink($linkUrl, $linkTitle);

        $description = $attachment['link']['description'];

        if (isset($attachment['link']['image_src'])) {
            $preview = $attachment['link']['image_src'];
            $url = $attachment['link']['url'];

            $description = $this->makeImg($preview, $url) . PHP_EOL . $description;
        }

        return PHP_EOL . 'Ссылка: ' . $link . PHP_EOL . $description;
    }

    /**
     * @param $attachment
     * @return string
     */
    private function makeNote(array $attachment): string
    {
        $noteLink = $attachment['note']['view_url'];
        $noteTitle = $attachment['note']['title'];

        return 'Заметка: ' . $this->makeLink($noteLink, $noteTitle);
    }

    /**
     * @param $attachment
     * @return string
     */
    private function makeApp(array $attachment): string
    {
        return "Приложение: {$attachment['app']['name']}";
    }

    /**
     * @param $attachment
     * @return string
     */
    private function makePoll(array $attachment): string
    {
        //        $answers = array_map(function ($answer) {
        //            return $answer['text'];
        //        }, $attachment['poll']['answers']);

        return "Опрос: {$attachment['poll']['question']}";
    }

    /**
     * @param $attachment
     * @return string
     */
    private function makePage(array $attachment): string
    {
        $pageLink = $attachment['page']['view_url'];
        $pageTitle = $attachment['page']['title'];

        return 'Страница: ' . $this->makeLink($pageLink, $pageTitle);
    }

    /**
     * @param $attachment
     * @return string
     */
    private function makeAlbum(array $attachment): string
    {
        $albumTitle = $attachment['album']['title'];
        $albumSize = $attachment['album']['size'];

        return "Альбом: $albumTitle ($albumSize фото)";
    }

    /**
     * @param $attachment
     * @return string
     */
    private function makePhotosList(array $attachment): string
    {
        return '[Список фотографий]';
    }
}
