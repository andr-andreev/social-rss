<?php


namespace SocialRss\Parser;

/**
 * Class ParserTrait
 * @package SocialRss\Parser
 */
trait ParserTrait
{
    private $regex = '/(^|){{pattern}}(\w*[[:alnum:]\@]+\w*)/u';

    /**
     * @param $img
     * @param null $link
     * @return string
     */
    protected function makeImg($img, $link = null)
    {
        $middlePart = "<img src='{$img}' />";

        return empty($link) ? $middlePart : $this->makeLink($link, $middlePart);
    }

    /**
     * @param $video
     * @param string $img
     * @return string
     */
    protected function makeVideo($video, $img = '')
    {
        return "<video src='$video' poster='$img' controls></video>";
    }

    /**
     * @param $href
     * @param $text
     * @return string
     */
    protected function makeLink($href, $text)
    {
        return "<a href='{$href}'>$text</a>";
    }


    /**
     * @param $pattern
     * @param $string
     * @return mixed
     */
    protected function getParsedByPattern($pattern, $string)
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
    private function parseByPattern($pattern, $template, $string)
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
    protected function makeBlock($avatar, $content)
    {
        return "<div style='display: flex;'>" .
        "<div style='width: 50px; margin-right: 10px;'>{$avatar}</div>" .
        "<div>{$content}</div>" .
        "</div>";
    }
}
