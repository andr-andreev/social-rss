<?php

declare(strict_types=1);


use Monolog\Handler\NativeMailerHandler;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Slim\Container;
use Slim\Views\PhpRenderer;
use SocialRss\Format\FormatFactory;
use SocialRss\Parser\ParserFactory;

/** @var Container $container */
$container = $app->getContainer();

$container['view'] = function (Container $container) {
    $templateVariables = [
        'router' => $container->get('router')
    ];

    return new PhpRenderer(__DIR__ . '/../views/', $templateVariables);
};

$container['parserFactory'] = function (Container $c) {
    return new ParserFactory();
};

$container['formatFactory'] = function (Container $c) {
    return new FormatFactory();
};

$container['logger'] = function ($c) {
    $logger = new Logger('logger');
    $config = $c['settings'];

    $handler = new NullHandler();
    if ($config['logger']['enabled']) {
        $handler = new NativeMailerHandler(
            $config['logger']['email'],
            'Social RSS error',
            $config['logger']['email']
        );
    }

    $logger->pushHandler($handler);

    return $logger;
};

$container['errorHandler'] = function ($c) {
    return new \SocialRss\Handler\Error($c['logger'], $c['settings']['displayErrorDetails']);
};

return $container;