<?php
declare(strict_types=1);


namespace SocialRss\Parser\Vk;

use SocialRss\Parser\Feed\BaseFeed;
use SocialRss\Parser\Vk\User\User;
use SocialRss\Parser\Vk\User\UserCollection;

/**
 * Class VkFeed
 * @package SocialRss\Parser\Vk
 */
class VkFeed extends BaseFeed
{
    protected $users;

    /**
     * VkFeed constructor.
     * @param $feed
     */
    public function __construct(array $feed)
    {
        parent::__construct($feed);
        $this->users = new UserCollection();

        $this->populateUsers();
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        $feed = $this->feed;

        // Prepare user's wall
        if (isset($feed['wall'])) {
            $feed = $this->processFeed($feed);
        }

        $feedItems = $feed['items'];
        $profiles = $this->users;

        return array_map(function ($item) use ($profiles) {
            return array_merge($item, ['profiles' => $profiles]);
        }, $feedItems);
    }

    /**
     * @param $feed
     * @return array
     */
    protected function processFeed(array $feed): array
    {
        $items = array_filter($feed['wall'], function ($item) {
            return is_array($item);
        });

        $processedItems = array_map(function ($item) {
            $item['type'] = $item['post_type'];
            $item['source_id'] = $item['from_id'];
            $item['post_id'] = $item['id'];

            return $item;
        }, $items);

        return array_merge($feed, ['items' => $processedItems]);
    }


    /**
     * @return void
     * @internal param $feed
     */
    public function populateUsers(): void
    {
        $feed = $this->feed;

        // Get groups array
        foreach ((array)$feed['groups'] as $group) {
            $this->users->addUser(
                new User(
                    -$group['id'],
                    $group['screen_name'],
                    $group['name'],
                    $group['photo_100'] ?: ''
                )
            );
        }

        foreach ((array)$feed['profiles'] as $profile) {
            $user = new User(
                $profile['id'],
                $profile['screen_name'] ?: '',
                "{$profile['first_name']} {$profile['last_name']}",
                $profile['photo_100']
            );
            $this->users->addUser($user);
        }
    }
}
