<?php
// router/web.php

use Bramus\Router\Router;
$router = new Router();

// Nếu project chạy trong thư mục con, nhớ setBasePath
$router->setBasePath('/phpcoban/BMCSDL-Final');

// Nạp controller
require_once __DIR__ . '/../controller/AuthController.php';

$router->post('/api/login', function() use ($conn) {
    (new AuthController($conn))->apiLogin();
});

// **API Logout** ← thêm cái này
$router->get('/api/logout', function() use ($conn) {
    (new AuthController($conn))->logout();
});

// (nếu bạn vẫn cần logout dạng form cũ)
$router->get('/logout', function() use ($conn) {
    (new AuthController($conn))->logout();
});

// 404
$router->set404(function() {
    http_response_code(404);
    echo json_encode(['error' => '404 Not Found']);
});
