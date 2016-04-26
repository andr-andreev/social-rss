<?php
namespace SocialRSS;

require_once '../vendor/autoload.php';

$feed = new Feed;

$map = ['twitter' => 'TwitterParser', 'vk' => 'VkParser'];

$source = isset($_GET['source']) ? $_GET['source'] : '';
array_key_exists($source, $map) ? $className = __NAMESPACE__ . '\\' . $map[$source] : exit('Invalid source');

$config = parse_ini_file("../.env", true);
$parser = new $className($feed, $config[$source]);

$feed->setChannelTitle($parser::NAME . ' RSS feed');
$feed->setChannelLink($parser::URL);

$parser->generateItems();

$feed->setHeader();
$feed->printFeed();