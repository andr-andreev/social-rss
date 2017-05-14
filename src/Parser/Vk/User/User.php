<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\User;


/**
 * Class User
 * @package SocialRss\Parser\Vk\User
 */
class User
{
    protected $id;
    protected $screenName;
    protected $name;
    protected $photoUrl;

    /**
     * User constructor.
     * @param int $id
     * @param string $screenName
     * @param string $name
     * @param string $photoUrl
     */
    public function __construct(int $id, string $screenName, string $name, string $photoUrl)
    {
        $this->id = self::normalizeId($id);
        $this->screenName = $screenName;
        $this->name = $name;
        $this->photoUrl = $photoUrl;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getScreenName(): string
    {
        return $this->screenName;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPhotoUrl(): string
    {
        return $this->photoUrl;
    }

    /**
     * @param $id
     * @return int
     */
    public static function normalizeId($id): int
    {
        return intval(abs($id));
    }

}