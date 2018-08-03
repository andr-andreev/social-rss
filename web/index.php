<?php
declare(strict_types=1);

namespace SocialRssApp;

use Slim\App;

require_once __DIR__ . '/../vendor/autoload.php';

$config = parse_ini_file('../.env', true, INI_SCANNER_TYPED);

$app = new App(['settings' => $config]);

include __DIR__ . '/../config/bootstrap.php';

include __DIR__ . '/../config/routes.php';

$app->run();
