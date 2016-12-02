<?php
declare(strict_types = 1);


namespace SocialRss\Parser\Vk;

use SocialRss\Parser\Vk\Posts\PostInterface;

/**
 * Class PostParser
 * @package SocialRss\Parser\Vk
 */
class PostParser
{
    /**
     * @var PostInterface
     */
    public $parser;

    private $item;
    private $users;

    private $typeMap = [
        'post' => Posts\PostPost::class,
        'photo' => Posts\PhotoPost::class,
        'photo_tag' => Posts\PhotoTagPost::class,
        'friend' => Posts\FriendPost::class,
        'note' => Posts\NotePost::class,
        'audio' => Posts\AudioPost::class,
        'video' => Posts\VideoPost::class,
    ];

    /**
     * PostParser constructor.
     * @param $item
     * @param $users
     */
    public function __construct($item, $users)
    {
        $this->item = $item;
        $this->users = $users;

        $map = $this->typeMap;
        $type = $this->item['type'];

        if (isset($map[$type])) {
            $this->parser = new $map[$type]($item, $users);
        }
    }

    /**
     * @return bool
     */
    public function isParserAvailable()
    {
        return isset($this->typeMap[$this->item['type']]);
    }
}
