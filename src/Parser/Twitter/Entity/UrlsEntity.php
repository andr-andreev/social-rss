<?php
declare(strict_types=1);


namespace SocialRss\Parser\Twitter\Entity;

use SocialRss\Helper\Html;

/**
 * Class UrlsEntity
 * @package SocialRss\Parser\Twitter\Entity
 */
class UrlsEntity extends AbstractEntity implements EntityInterface
{
    /**
     * @return string
     */
    public function getParsedContent()
    {
        return $this->replaceContent(
            $this->text,
            $this->item['url'],
            Html::link($this->item['expanded_url'], $this->item['display_url'])
        );
    }
}
