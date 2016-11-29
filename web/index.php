<?php
declare(strict_types = 1);

namespace SocialRss;

use SocialRss\Parser\Parser;
use SocialRss\Format\Format;
use SocialRss\Exception\SocialRssException;

require_once __DIR__ . '/../vendor/autoload.php';

$source = $_GET['source'] ?? '';
$username = $_GET['username'] ?? '';
$output = $_GET['output'] ?? 'rss';

$config = parse_ini_file("../.env", true);

if (!isset($config[$source])) {
    throw new SocialRssException("No config found for $source source");
}

$parser = new Parser($source, $config[$source]);
$writer = new Format($output);

$feed = $parser->getFeed($username);

$parsedFeed = $parser->parseFeed($feed);

echo $writer->format($parsedFeed);
