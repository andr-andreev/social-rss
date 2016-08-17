<?php
namespace SocialRss;

use SocialRss\Exception\SocialRssException;

require_once __DIR__ . '/../vendor/autoload.php';

$source = isset($_GET['source']) ? $_GET['source'] : '';
$output = isset($_GET['output']) ? $_GET['output'] : 'rss';

$config = parse_ini_file("../.env", true);

if (!isset($config[$source])) {
    throw new SocialRssException("No config found for $source source");
}

$parser = new Parser\Parser($source, $config[$source]);
$writer = new Format\Format($output);

$feed = $parser->getFeed();

$parsedFeed = $parser->parseFeed($feed);

echo $writer->format($parsedFeed);
