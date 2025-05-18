<?php
// config/init.php
// session_start();

// load cấu hình
$config = require __DIR__ . '/config.php';

// kết nối MySQLi
$conn = new mysqli(
    $config['host'],
    $config['username'],
    $config['password'],
    $config['dbname']
);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset($config['charset']);
