<?php
declare(strict_types=1);


namespace SocialRss\Data;

use SocialRss\Parser\Post\PostInterface;
use Spatie\DataTransferObject\DataTransferObject;

class PostData extends DataTransferObject
{
    /** @var string */
    public $title;

    /** @var string */
    public $link;

    /** @var string */
    public $content;

    /** @var \SocialRss\Data\PostData|null */
    public $quote;

    /** @var \DateTime|null */
    public $date;

    /** @var string[]|null */
    public $tags;

    /** @var \SocialRss\Data\AuthorData|null */
    public $author;

    public static function fromResponse(PostInterface $post): PostData
    {
        return new self([
            'title' => $post->getTitle(),
            'link' => $post->getLink(),
            'content' => $post->getContent(),
            'quote' => $post->getQuote(),
            'date' => $post->getDate(),
            'tags' => $post->getTags(),
            'author' => new AuthorData([
                'name' => $post->getAuthorName(),
                'avatar' => $post->getAuthorAvatar(),
                'link' => $post->getAuthorLink(),
            ])
        ]);
    }
}
