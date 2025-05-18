<?php
// public/index.php

session_start();

// --- Bỏ dòng này nếu không có vendor/autoload.php ---
// require_once __DIR__ . '/../vendor/autoload.php';

// Kết nối CSDL
require_once __DIR__ . '/database/init.php';

// Khởi tạo router
require_once __DIR__ . '/vendor/bramus/router/src/Bramus/Router/Router.php';
use Bramus\Router\Router;

$router = new Router();

// Nạp route
require_once __DIR__ . '/router/web.php';

// Chạy router
$router->run();
