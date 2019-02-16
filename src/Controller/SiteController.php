<?php

declare(strict_types=1);


namespace SocialRss\Controller;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;
use SocialRss\Exception\SocialRssException;
use SocialRss\Format\FormatFactory;
use SocialRss\Parser\ParserFactory;

class SiteController
{
    /** @var array */
    protected $config;

    /** @var ParserFactory */
    protected $parserFactory;

    /** @var FormatFactory */
    protected $formatFactory;

    /** @var PhpRenderer */
    protected $view;

    public function __construct(ContainerInterface $container)
    {
        $this->config = $container->get('settings');
        $this->parserFactory = $container->get('parserFactory');
        $this->formatFactory = $container->get('formatFactory');
        $this->view = $container->get('view');
    }

    public function index(Request $request, Response $response, $args)
    {
        return $this->view->render($response, 'index.php', [
            'parsers' => $this->parserFactory->getParsersList(),
        ]);
    }

    public function feed(Request $request, Response $response)
    {
        $source = $request->getAttribute('source');
        $params = $request->getQueryParams();

        $username = $params['username'] ?? '';
        $output = $params['output'] ?? 'rss';

        if (!isset($this->config[$source])) {
            throw new SocialRssException("No config found for $source source");
        }

        $parser = $this->parserFactory->create($source, $this->config[$source]);
        $writer = $this->formatFactory->create($output);

        $feed = $parser->getFeed($username);
        $parsedFeed = $parser->parseFeed($feed);

        $response->getBody()->write($writer->format($parsedFeed));

        return $response;
    }

    public function favicon(Request $request, Response $response, $args)
    {
        return $response->withStatus(404);
    }
}
