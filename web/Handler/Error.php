<?php
declare(strict_types=1);

namespace SocialRssApp\Handler;

use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class Error
 * @package SocialRssApp\Handler
 */
final class Error extends \Slim\Handlers\Error
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Error constructor.
     * @param Logger $logger
     */
    public function __construct(Logger $logger, bool $displayErrorDetails)
    {
        parent::__construct($displayErrorDetails);

        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param \Exception $exception
     * @return Response
     */
    public function __invoke(Request $request, Response $response, \Exception $exception)
    {
        $this->logger->critical($exception);

        return parent::__invoke($request, $response, $exception);
    }
}
