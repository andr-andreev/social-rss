<?php
declare(strict_types = 1);


namespace SocialRss\Parser;

/**
 * Class ParserTrait
 * @package SocialRss\Parser
 */
trait ParserTrait
{
    private $regex = '/(^|){{pattern}}(\w*[[:alnum:]\@\.]+\w*)/u';

    /**
     * @param string $img
     * @param string $link
     * @return string
     */
    protected function makeImg(string $img, string $link = ''): string
    {
        $middlePart = "<img src='{$img}'>";

        return empty($link) ? $middlePart : $this->makeLink($link, $middlePart);
    }

    /**
     * @param $video
     * @param string $poster
     * @return string
     */
    protected function makeVideo(string $video, string $poster = ''): string
    {
        return "<video src='$video' poster='$poster' controls></video>";
    }

    /**
     * @param $href
     * @param $text
     * @return string
     */
    protected function makeLink(string $href, string $text): string
    {
        return "<a href='{$href}'>$text</a>";
    }


    /**
     * @param $pattern
     * @param $string
     * @return mixed
     */
    protected function getParsedByPattern(string $pattern, string $string): array
    {
        $symbol = str_replace('{string}', '', $pattern);
        preg_match_all('/(^|)' . $symbol . '(\w*[[:alnum:]]+\w*)/u', $string, $out);

        $array = $out[2];

        return $array;
    }

    /**
     * @param $pattern
     * @param $template
     * @param $string
     * @return mixed
     */
    private function parseByPattern(string $pattern, string $template, string $string): string
    {
        $regex = str_replace('{{pattern}}', $pattern, $this->regex);

        $replacement = str_replace('{{string}}', '\2', $template);

        return preg_replace($regex, $replacement, $string);
    }

    /**
     * @param $avatar
     * @param $content
     * @return string
     */
    protected function makeBlock(string $avatar, string $content): string
    {
        return "<div style='display: flex;'>" .
            "<div style='width: 50px; margin-right: 10px;'>{$avatar}</div>" .
            "<div>{$content}</div>" .
            "</div>";
    }
}
