<?php

use Framework\Router;

require __DIR__ . '/../vendor/autoload.php';
require '../helpers.php';

// Instantiating the Router
$router = new Router();

// Get routes
require basePath('routes.php');

// Get current URI and HTTP Method
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

// Route the request
$router->route($uri);