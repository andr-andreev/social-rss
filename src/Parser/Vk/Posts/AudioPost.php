<?php


namespace SocialRss\Parser\Vk\Posts;

class AudioPost extends AbstractPost implements PostInterface
{
    public function getTitle()
    {
        return $this->getUserName() . ': новые аудиозаписи';
    }

    public function getLink()
    {
        return self::URL . "audios{$this->users[$this->item['source_id']]['id']}";
    }

    public function getDescription()
    {
        $audios = $this->item['audio'];

        $audios = array_filter($audios, function ($audio) {
            return isset($audio['title']);
        });

        $audios = array_map(function ($audio) {
            return "Аудиозапись: {$audio['artist']} &ndash; {$audio['title']}";
        }, $audios);

        return implode(PHP_EOL, $audios);
    }
}
