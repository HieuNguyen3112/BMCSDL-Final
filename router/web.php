<?php
// router/web.php

use Bramus\Router\Router;
$router = new Router();

// Nếu project chạy trong thư mục con, nhớ setBasePath
$router->setBasePath('/phpcoban/BMCSDL-Final');

// Nạp controller
require_once __DIR__ . '/../controller/AuthController.php';
require_once __DIR__ . '/../controller/ProfileController.php';
require_once __DIR__ . '/../controller/EmployeeController.php';
// Nạp middlewhare
// require_once __DIR__ . '/../middleware/AuthMiddleware.php';
// require_once __DIR__ . '/../middleware/RoleMiddleware.php';

//Đăng nhập
$router->post('/api/login', function() use ($conn) {
    (new AuthController($conn))->apiLogin();
});

// **API Logout** ← thêm cái này
$router->get('/api/logout', function() use ($conn) {
    (new AuthController($conn))->logout();
});

// API danh sách nhân viên & trưởng phòng
$router->get('/api/employees/nhanvien', function() use ($conn) {
    (new EmployeeController($conn))->apiListRoleNhanVien();
});

// API lấy profile cá nhân JSON
$router->get('/api/profile', function() use ($conn) {
    (new ProfileController($conn))->apiShow();
});
$router->put('/api/profile', function() use ($conn) {
    (new ProfileController($conn))->apiUpdate();
});

// API cập nhật thông tin nhân viên
$router->put('/api/employees/update', function() use ($conn) {
    (new EmployeeController($conn))->apiUpdate();
});

// API tạo nhân viên
$router->post('/api/employees/create', function() use ($conn) {
    (new EmployeeController($conn))->apiCreate();
});


// 404
$router->set404(function() {
    http_response_code(404);
    echo json_encode(['error' => '404 Not Found']);
});
