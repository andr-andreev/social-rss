<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk\User;

class User
{
    protected $id;
    protected $screenName;
    protected $name;
    protected $photoUrl;

    public function __construct(int $id, string $screenName, string $name, string $photoUrl)
    {
        $this->id = $id;
        $this->screenName = $screenName;
        $this->name = $name;
        $this->photoUrl = $photoUrl;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getScreenName(): string
    {
        return $this->screenName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhotoUrl(): string
    {
        return $this->photoUrl;
    }
}
