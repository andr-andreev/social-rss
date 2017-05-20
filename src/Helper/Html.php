<?php
declare(strict_types=1);


namespace SocialRss\Helper;

/**
 * Class Html
 *
 * @package SocialRss\Parser
 */
class Html
{
    private static $regex = '/(^|){{pattern}}(\w*[[:alnum:]\@\.]+\w*)/u';

    /**
     * @param string $img
     * @param string $link
     * @return string
     */
    public static function img(string $img, string $link = ''): string
    {
        $middlePart = "<img src='{$img}'>";

        return empty($link) ? $middlePart : self::link($link, $middlePart);
    }

    /**
     * @param $video
     * @param string $poster
     * @return string
     */
    public static function video(string $video, string $poster = ''): string
    {
        return "<video src='$video' poster='$poster' controls></video>";
    }

    /**
     * @param $href
     * @param $text
     * @return string
     */
    public static function link(string $href, string $text): string
    {
        return "<a href='{$href}'>$text</a>";
    }


    /**
     * @param $pattern
     * @param $string
     * @return mixed
     */
    public static function getParsedByPattern(string $pattern, string $string): array
    {
        $symbol = str_replace('{string}', '', $pattern);
        preg_match_all('/(^|)' . $symbol . '(\w*[[:alnum:]]+\w*)/u', $string, $out);

        return $out[2];
    }

    /**
     * @param $pattern
     * @param $template
     * @param $string
     * @return mixed
     */
    public static function parseByPattern(string $pattern, string $template, string $string): string
    {
        $regex = str_replace('{{pattern}}', $pattern, self::$regex);

        $replacement = str_replace('{{string}}', '\2', $template);

        $result = preg_replace($regex, $replacement, $string);

        return $result ?: '';
    }

    /**
     * @param $avatar
     * @param $content
     * @return string
     */
    public static function makeBlock(string $avatar, string $content): string
    {
        return "<div style='display: flex;'>" .
            "<div style='width: 50px; margin-right: 10px;'>{$avatar}</div>" .
            "<div>{$content}</div>" .
            "</div>";
    }

    /**
     * @param string $avatarImgSrc
     * @param string $avatarImgLink
     * @return string
     * @internal param $item
     */
    public static function makeAvatar(string $avatarImgSrc, string $avatarImgLink): string
    {
        return Html::img($avatarImgSrc, $avatarImgLink);
    }

    /**
     * @param $html
     * @return string
     */
    public static function blockquote($html): string
    {
        return "<blockquote>{$html}</blockquote>";
    }
}
