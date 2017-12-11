<?php
declare(strict_types=1);

namespace SocialRssApp;

use Monolog\Handler\NativeMailerHandler;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Slim\App;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;
use SocialRss\Exception\SocialRssException;
use SocialRss\Format\FormatFactory;
use SocialRss\Format\FormatInterface;
use SocialRss\Parser\ParserFactory;
use SocialRss\Parser\ParserInterface;

require_once __DIR__ . '/../vendor/autoload.php';

$config = parse_ini_file('../.env', true, INI_SCANNER_TYPED);

$app = new App(['settings' => $config]);

// Retrieve container instance
$container = $app->getContainer();

$container['view'] = function (Container $container) {
    $templateVariables = [
        'router' => $container->get('router')
    ];

    return new PhpRenderer(__DIR__ . '/../views/', $templateVariables);
};

// Register parserFactory
$container['parserFactory'] = function (Container $c) {
    return new ParserFactory();
};

// Register formatFactory
$container['formatFactory'] = function (Container $c) {
    return new FormatFactory();
};

// Register monolog
$container['logger'] = function ($c) {
    $logger = new Logger('logger');
    $config = $c['settings'];

    $handler = new NullHandler();
    if ($config['logger']['enabled']) {
        $handler = new NativeMailerHandler(
            $config['logger']['email'],
            'PHP Social RSS error',
            $config['logger']['email']
        );
    }

    $logger->pushHandler($handler);

    return $logger;
};

$container['errorHandler'] = function ($c) {
    return new Handler\Error($c['logger'], $c['settings']['displayErrorDetails']);
};

// Render PHP template
$app->get('/', function (Request $request, Response $response, $args) {
    /** @var $parserFactory ParserFactory */
    $parserFactory = $this->get('parserFactory');

    return $this->view->render($response, 'index.php', [
        'parsers' => $parserFactory->getParsersList(),
    ]);
})->setName('index');

$app->get('/favicon.ico', function (Request $request, Response $response, $args) {
    return $response->withStatus(404);
})->setName('favicon');

$app->get('/feed/{source}', function (Request $request, Response $response) {
    $source = $request->getAttribute('source');
    $params = $request->getQueryParams();

    $username = $params['username'] ?? '';
    $output = $params['output'] ?? 'rss';

    $config = $this->get('settings');

    if (!isset($config[$source])) {
        throw new SocialRssException("No config found for $source source");
    }

    /** @var ParserInterface $parser */
    $parser = $this->get('parserFactory')->create($source, $config[$source]);

    /** @var FormatInterface $writer */
    $writer = $this->get('formatFactory')->create($output);

    $feed = $parser->getFeed($username);
    $parsedFeed = $parser->parseFeed($feed);

    $response->getBody()->write($writer->format($parsedFeed));

    return $response;
})->setName('parser');

$app->run();
