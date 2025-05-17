<?php
// index.php
require_once __DIR__ . '/database/init.php';
require_once __DIR__ . '/controller/AuthController.php';

$auth   = new AuthController($conn);
$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->doLogin();
        } else {
            $auth->showLogin();
        }
        break;

    case 'profile':
        $auth->profile();
        break;

    case 'logout':
        $auth->logout();
        break;

    default:
        header('HTTP/1.0 404 Not Found');
        echo 'Không tìm thấy trang';
        exit;
}
