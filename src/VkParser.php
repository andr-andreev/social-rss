<?php

namespace SocialRSS;

class VkParser extends Parser
{
    const NAME = 'VK';
    const URL = 'https://vk.com/';

    const API_METHOD = 'newsfeed.get';
    const API_PARAMETERS = ['count' => 100];

    private $users = [];

    public function __construct($feed, $config)
    {
        parent::__construct($feed);

        try {
            $vk = new \VK\VK($config['app_id'], $config['api_secret'], $config['access_token']);
            $socialFeed = $vk->api(self::API_METHOD, self::API_PARAMETERS);

            if (isset($socialFeed['error'])) {
                throw new \Exception($socialFeed['error']['error_msg']);
            }
        } catch (\Exception $error) {
            exit($error->getMessage());
        }

        $this->socialFeed = $socialFeed['response']['items'];

        foreach ($socialFeed['response']['profiles'] as $profile) {
            $uid = $profile['uid'];
            $this->users[$uid] = $profile;
            $this->users[$uid]['id'] = $uid;
            $this->users[$uid]['name'] = "{$this->users[$uid]['first_name']} {$this->users[$uid]['last_name']}";
        }

        foreach ($socialFeed['response']['groups'] as $group) {
            $gid = -$group['gid'];
            $this->users[$gid] = $group;
            $this->users[$gid]['id'] = $gid;
        }
    }

    protected function generateItem($item)
    {
        $map = [
            'post' => [
                'title' => '',
                'link' => function ($item) {
                    return self::URL . "wall{$this->users[$item['source_id']]['id']}_{$item['post_id']}";
                },
                'description' => function ($item) {
                    $description = '';

                    if (isset($item['copy_text'])) {
                        $description .= $this->parseContent($item['copy_text']) . PHP_EOL . PHP_EOL;
                    }

                    if (isset($item['copy_owner_id'])) {
                        $description .= 'Репост от ' . $this->makeLink(self::URL . $this->users[$item['copy_owner_id']]['screen_name'], $this->users[$item['copy_owner_id']]['name']) . ':' . PHP_EOL;
                    }

                    $description .= $this->parseContent($item['text']);

                    return $description;
                },
            ],
            'photo' => [
                'title' => 'новые фотографии',
                'link' => function ($item) {
                    return self::URL . $this->users[$item['source_id']]['screen_name'];
                },
                'description' => function ($item) {
                    return $this->makePhotos($item['photos']);
                },
            ],
            'photo_tag' => [
                'title' => 'новые отметки',
                'link' => function ($item) {
                    return self::URL . $this->users[$item['source_id']]['screen_name'];
                },
                'description' => function ($item) {
                    return $this->makePhotos($item['photo_tags']);
                },
            ],
            'friend' => [
                'title' => 'новые друзья',
                'link' => function ($item) {
                    return self::URL . "friends?id={$this->users[$item['source_id']]['id']}";
                },
                'description' => function ($item) {
                    $description = '';

                    if (isset($item['friends'])) {
                        foreach ($item['friends'] as $friend) {
                            if (isset($friend['uid'])) {
                                $description .= $this->makeFriends($friend['uid']) . PHP_EOL;
                            }
                        }
                    }

                    return $description;
                },
            ],
            'note' => [
                'title' => 'новая заметка',
                'link' => function ($item) {
                    return self::URL . "{$this->users[$item['source_id']]['screen_name']}";
                },
                'description' => function ($item) {
                    $description = '';

                    foreach ($item['notes'] as $note) {
                        $description .= 'Заметка: ' . $this->makeLink($note['view_url'], $note['title']) . PHP_EOL;
                    }

                    return $description;
                },
            ],
            'audio' => [
                'title' => 'новые аудиозаписи',
                'link' => function ($item) {
                    return self::URL . "audios{$this->users[$item['source_id']]['id']}";
                },
                'description' => function ($item) {
                    $description = '';

                    foreach ($item['audio'] as $audio) {
                        if (isset($audio['title'])) {
                            $description .= "Аудиозапись: {$audio['artist']} &ndash; {$audio['title']}" . PHP_EOL;
                        }
                    }

                    return $description;
                },
            ],
            'video' => [
                'title' => 'новые видеозаписи',
                'link' => function ($item) {
                    return self::URL . "videos{$this->users[$item['source_id']]['id']}";
                },
                'description' => function ($item) {
                    $description = '';

                    foreach ($item['video'] as $video) {
                        if (isset($video['title'])) {
                            $description .= $this->makeVideoAttach($video) . PHP_EOL;
                        }
                    }

                    return $description;
                },
            ],
        ];

        $attachmentMap = [
            'photo' => function ($attachment) {
                return $this->makeImg($attachment['photo']['src_big']);
            },
            'posted_photo' => function ($attachment) {
                return $this->makeImg($attachment['posted_photo']['photo_604']);
            },
            'video' => function ($attachment) {
                return $this->makeVideoAttach($attachment['video']);
            },
            'audio' => function ($attachment) {
                return "Аудиозапись: {$attachment['audio']['artist']} &ndash; {$attachment['audio']['title']}";
            },
            'doc' => function ($attachment) {
                return 'Документ: ' . $this->makeLink($attachment['doc']['url'], $attachment['doc']['title']);
            },
            'graffiti' => function ($attachment) {
                return 'Граффити: ' . $this->makeImg($attachment['graffiti']['photo_586']);
            },
            'link' => function ($attachment) {
                $attachParsed = PHP_EOL . 'Ссылка: ' . $this->makeLink($attachment['link']['url'], $attachment['link']['title']);

                if (isset($attachment['link']['image_src'])) {
                    $attachParsed .= $this->makeBlock($this->makeImg($attachment['link']['image_src'], $attachment['link']['url']), $attachment['link']['description']);
                } else {
                    $attachParsed .= PHP_EOL . $attachment['link']['description'];
                }

                return $attachParsed;
            },
            'note' => function ($attachment) {
                return 'Заметка: ' . $this->makeLink($attachment['note']['view_url'], $attachment['note']['title']);
            },
            'app' => function ($attachment) {
                return "Приложение: {$attachment['app']['name']}";
            },
            'poll' => function ($attachment) {
                return "Опрос: {$attachment['poll']['question']}";
            },
            'page' => function ($attachment) {
                return 'Страница: ' . $this->makeLink($attachment['page']['view_url'], $attachment['page']['title']);
            },
            'album' => function ($attachment) {
                return "Альбом: {$attachment['album']['title']} ({$attachment['album']['size']} фото)";
            },
            'photos_list' => function ($attachment) {
                return '[Список фотографий]';
            },
        ];

        if (!isset($map[$item['type']])) {
            return;
        }

        $title = $this->users[$item['source_id']]['name'];
        $titleType = $map[$item['type']]['title'] ?: '';
        if (!empty($titleType)) {
            $title .= ": $titleType";
        }

        $link = $map[$item['type']]['link']($item) ?: '';
        $description = $map[$item['type']]['description']($item) ?: '';

        if (isset($item['attachments'])) {
            $attachments = array_map(function ($attachment) use ($attachmentMap) {
                return isset($attachmentMap[$attachment['type']]) ? $attachmentMap[$attachment['type']]($attachment) : "[Item contains unknown attachment type {$attachment['type']}]";
            }, $item['attachments']);

            $description .= PHP_EOL . implode(PHP_EOL, $attachments);
        }

        $avatar = $this->makeImg($this->users[$item['source_id']]['photo'], self::URL . $this->users[$item['source_id']]['screen_name']);
        $content = nl2br(trim($description));
        $description = $this->makeBlock($avatar, $content);

        $feedItem = new Item();
        $feedItem->setTitle($title);
        $feedItem->setLink($link);
        $feedItem->setDescription($description);
        $feedItem->setAuthor($this->users[$item['source_id']]['name']);
        $feedItem->setDate($item['date']);

        return $feedItem;
    }

    private function parseContent($text)
    {
        // Match URLs
        $text = preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1">$1</a>', $text);

        // Match user tags [id1|User]
        preg_match_all('/\\[(.*?)\\]/', $text, $matches);
        foreach ($matches[0] as $key => $match) {
            $list = explode('|', $matches[1][$key]);
            if (count($list) == 2) {
                list($user_id, $tag) = $list;
                $text = str_replace($match, $this->makeLink(self::URL . $user_id, $tag), $text);
            }
        }

        // Match #hashtags
        $text = preg_replace('/(^|)#(\w*[a-zA-Zа-яА-Я_]+\w*)/u', '\1<a href="https://vk.com/feed?section=search&q=%23\2">#\2</a>', $text);

        return $text;
    }

    private function makePhotos($items)
    {
        $out = '';
        foreach ($items as $photo) {
            if (isset($photo['pid'])) {
                $out .= $this->makeImg($photo['src_big'], self::URL . "photo{$photo['owner_id']}_{$photo['pid']}") . PHP_EOL;
            }
        }

        return $out;
    }

    private function makeFriends($user_id)
    {
        return $this->makeLink(self::URL . $this->users[$user_id]['screen_name'], $this->users[$user_id]['name'] . PHP_EOL . $this->makeImg($this->users[$user_id]['photo']));
    }

    private function makeVideoAttach($attachment)
    {
        $attachment = $this->makeImg($attachment['image'])
            . 'Видеозапись: ' . $this->makeLink(self::URL . "video{$attachment['owner_id']}_{$attachment['vid']}", $attachment['title']) . ' (' . gmdate('H:i:s', $attachment['duration']) . ')';

        return $attachment;
    }
}
