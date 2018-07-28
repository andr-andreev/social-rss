<?php

declare(strict_types=1);

use SocialRss\Controller\SiteController;

$app->get('/', SiteController::class . ':index')
    ->setName('index');

$app->get('/favicon.ico', SiteController::class . ':favicon')
    ->setName('favicon');

$app->get('/feed/{source}', SiteController::class . ':feed')
    ->setName('parser');