<?php
// Bai02/init.php

// 1. Nạp config và Database class (cùng cấp)
$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/model/database.php';

// 2. Khởi tạo Database và lấy PDO
$db  = new Database($config);
$conn = $db->getConnection();

// // 2. Định nghĩa InitDatabase giống như trước
// class InitDatabase extends Database {
//   public function __construct(array $config) {
//     parent::__construct($config);
//   }
//   public function createStructure() {
//     $sql = "
//         CREATE TABLE IF NOT EXISTS `user` (
//         `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//         `username` VARCHAR(100) NOT NULL UNIQUE,
//         `password` VARCHAR(255) NOT NULL,
//         `firstname` VARCHAR(50) DEFAULT NULL,
//         `lastname` VARCHAR(50) DEFAULT NULL,
//         `create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
//         `last_login` DATETIME DEFAULT NULL,
//         `role` TINYINT(1) NOT NULL DEFAULT 0    -- KHÔNG PHẨI DẤU PHẨY Ở CUỐI DÒNG NÀY
//         ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
//         ";
//     $this->set_query($sql);
//     if ($this->excute_query()) {
//       echo "<p style='color:lime;'>✅ Bảng `user` đã được tạo hoặc đã tồn tại.</p>";
//     } else {
//       echo "<p style='color:red;'>❌ Lỗi: " . $this->conn->error . "</p>";
//     }
//     $this->close();
//   }
// }

// // 3. Chạy
// $initializer = new InitDatabase($config);
// $initializer->createStructure();
