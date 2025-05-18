<?php
// router/web.php

use Bramus\Router\Router;
$router = new Router();

// Nếu project chạy trong thư mục con, nhớ setBasePath
$router->setBasePath('/phpcoban/BMCSDL-Final');

// Nạp controller
require_once __DIR__ . '/../controller/AuthController.php';

// API login
$router->post('/api/login', function() use ($conn) {
    header('Content-Type: application/json; charset=utf-8');
    (new AuthController($conn))->apiLogin();
});

// Web login form
$router->get('/signin', function() use ($conn) {
    (new AuthController($conn))->showLogin();
});

// Xử lý login form
$router->post('/signin', function() use ($conn) {
    (new AuthController($conn))->doLogin();
});

// Profile
$router->get('/profile', function() use ($conn) {
    (new AuthController($conn))->profile();
});

// Logout
$router->get('/logout', function() use ($conn) {
    (new AuthController($conn))->logout();
});

// 404
$router->set404(function() {
    http_response_code(404);
    echo json_encode(['error' => '404 Not Found']);
});
