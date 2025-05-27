<?php
// database/createAuditLogTable.php

require_once __DIR__ . '/init.php';

try {
    // 1) Tạo bảng audit_log
    $sql = "
    CREATE TABLE IF NOT EXISTS `audit_log` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `action`     VARCHAR(20)     NOT NULL,                -- kiểu thao tác: UPDATE, DELETE, ...
      `table_name` VARCHAR(50)     NOT NULL,                -- tên bảng bị tác động
      `record_id`  INT             NOT NULL,                -- id của record
      `old_data`   JSON            NULL,                    -- dữ liệu trước khi thay đổi
      `new_data`   JSON            NULL,                    -- dữ liệu sau khi thay đổi
      `user_id`    INT             NOT NULL,                -- mã NV thực hiện
      `user_name`  VARCHAR(100)    NULL,                    -- họ tên NV
      `user_role`  VARCHAR(50)     NULL,                    -- role của NV
      `ip_address` VARCHAR(45)     NULL,                    -- địa chỉ IP
      `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    if (! $conn->query($sql)) {
        throw new Exception("Lỗi khi tạo bảng audit_log: " . $conn->error);
    }
    echo "✓ Bảng `audit_log` đã được tạo (nếu chưa có) thành công.<br>";

    // 2) (Tuỳ chọn) Tạo index để tăng tốc truy vấn theo record_id và user_id
    $indexes = [
      "CREATE INDEX IF NOT EXISTS idx_audit_record ON audit_log(record_id);",
      "CREATE INDEX IF NOT EXISTS idx_audit_user   ON audit_log(user_id);"
    ];
    foreach ($indexes as $idxSql) {
      if (! $conn->query($idxSql) && strpos($conn->error, 'Duplicate') === false) {
        throw new Exception("Lỗi khi tạo index: " . $conn->error);
      }
    }
    echo "✓ Các index đã được tạo (nếu chưa có).<br>";

} catch (Exception $e) {
    echo "<p style='color:red;'>‼ " . $e->getMessage() . "</p>";
}

// Đóng kết nối
$conn->close();
