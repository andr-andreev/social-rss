<?php
declare(strict_types = 1);

namespace SocialRss;

use Slim\App;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use SocialRss\Exception\SocialRssException;
use SocialRss\Format\FormatFactory;
use SocialRss\Format\FormatInterface;
use SocialRss\Parser\ParserFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$config = parse_ini_file("../.env", true, INI_SCANNER_TYPED);

$app = new App(['settings' => $config]);

// Retrieve container instance
$container = $app->getContainer();

// Register parserFactory
$container['parserFactory'] = function (Container $c) {
    return new ParserFactory();
};

// Register formatFactory
$container['formatFactory'] = function (Container $c) {
    return new FormatFactory();
};

$app->get('/{source}', function (Request $request, Response $response) {
    $source = $request->getAttribute('source');
    $params = $request->getQueryParams();

    $username = $params['username'] ?? '';
    $output = $params['output'] ?? 'rss';

    $config = $this->get('settings');

    if (!isset($config[$source])) {
        throw new SocialRssException("No config found for $source source");
    }

    /* @var $parser \SocialRss\Parser\ParserInterface */
    $parser = $this->get('parserFactory')->create($source, $config[$source]);

    /* @var $writer FormatInterface */
    $writer = $this->get('formatFactory')->create($output);

    $feed = $parser->getFeed($username);
    $parsedFeed = $parser->parseFeed($feed);

    $response->getBody()->write($writer->format($parsedFeed));

    return $response;
});

$app->run();
