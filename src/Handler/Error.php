<?php
declare(strict_types=1);

namespace SocialRss\Handler;

use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Error extends \Slim\Handlers\Error
{
    /**
     * @var Logger
     */
    protected $logger;

    public function __construct(Logger $logger, bool $displayErrorDetails)
    {
        parent::__construct($displayErrorDetails);

        $this->logger = $logger;
    }

    public function __invoke(Request $request, Response $response, \Exception $exception)
    {
        $this->logger->critical($exception);

        return parent::__invoke($request, $response, $exception);
    }
}
