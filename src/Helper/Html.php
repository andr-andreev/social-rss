<?php
declare(strict_types=1);


namespace SocialRss\Helper;

class Html
{
    /** @var string */
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
        return "<video src='$video' poster='$poster' controls autoplay muted></video>";
    }

    public static function link(string $href, string $text): string
    {
        return "<a href='{$href}' rel='noopener noreferrer' referrerpolicy='no-referrer'>$text</a>";
    }

    public static function getParsedByPattern(string $pattern, string $string): array
    {
        $symbol = str_replace('{string}', '', $pattern);
        preg_match_all('/(^|)' . $symbol . '(\w*[[:alnum:]]+\w*)/u', $string, $out);

        return $out[2];
    }

    public static function parseByPattern(string $startsWith, string $template, string $subject): string
    {
        if (empty($subject)) {
            return '';
        }

        $regex = str_replace('{{pattern}}', $startsWith, self::$regex);

        $result = preg_replace_callback($regex, function ($matches) use ($template) {
            $href = str_replace('{{string}}', $matches[2], $template);
            $text = $matches[0];

            return Html::link($href, $text);
        }, $subject);

        return $result ?: '';
    }

    public static function makeBlock(string $avatar, string $content): string
    {
        return <<<HTML
<div style="display: flex; flex-direction: row-reverse;">
    <div>{$content}</div>
    <div style="max-width: 50px; margin-right: 10px;">{$avatar}</div>
</div>
HTML;
    }

    public static function makeAvatar(string $avatarImgSrc, string $avatarImgLink): string
    {
        return Html::img($avatarImgSrc, $avatarImgLink);
    }

    public static function blockquote($html): string
    {
        return "<blockquote>{$html}</blockquote>";
    }
}
