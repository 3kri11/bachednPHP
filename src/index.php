<?php

declare(strict_types=1);

use Framework\Router\Router;

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, PUT,POST, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require '../helpers/helpers.php';
require getPath() . 'vendor/autoload.php';
$config = require getPath() . 'helpers/config.php';

$db = new Framework\db\Database($config);

$router = new Router();

require '../Framework/Router/routes.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$list = $router->route($uri);

echo json_encode($list);
