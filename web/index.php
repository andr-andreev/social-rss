<?php
declare(strict_types = 1);

namespace SocialRss;

use Slim\Http\Request;
use Slim\Http\Response;
use SocialRss\Parser\Parser;
use SocialRss\Format\Format;
use SocialRss\Exception\SocialRssException;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
    ]
]);

$app->get('/{source}', function (Request $request, Response $response) {
    $source = $request->getAttribute('source');
    $params = $request->getQueryParams();

    $username = $params['username'] ?? '';
    $output = $params['output'] ?? 'rss';

    $config = parse_ini_file("../.env", true);

    if (!isset($config[$source])) {
        throw new SocialRssException("No config found for $source source");
    }

    $parser = new Parser($source, $config[$source]);
    $writer = new Format($output);

    $feed = $parser->getFeed($username);
    $parsedFeed = $parser->parseFeed($feed);

    $response->getBody()->write($writer->format($parsedFeed));

    return $response;
});

$app->run();
