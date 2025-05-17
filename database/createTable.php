<?php
// database/createTable.php

require_once __DIR__ . '/init.php';

try {

    $stmts = [

        // Bảng NHANVIEN
        "CREATE TABLE IF NOT EXISTS NHANVIEN (
            MaNhanVien INT AUTO_INCREMENT PRIMARY KEY,
            HoTen NVARCHAR(100),
            GioiTinh NVARCHAR(3),
            NgaySinh DATE,
            SoDienThoai VARCHAR(15),
            Luong VARBINARY(255),
            PhuCap VARBINARY(255),
            MaSoThue VARCHAR(20),
            MaChucVu INT,
            MaPhong INT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        // Bảng TAIKHOAN
        "CREATE TABLE IF NOT EXISTS TAIKHOAN (
            MaNhanVien INT PRIMARY KEY,
            MatKhau VARBINARY(255),
            CONSTRAINT FK_TAIKHOAN_NHANVIEN
              FOREIGN KEY (MaNhanVien)
              REFERENCES NHANVIEN(MaNhanVien)
              ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        // Bảng PHONGBAN
        "CREATE TABLE IF NOT EXISTS PHONGBAN (
            MaPhong INT AUTO_INCREMENT PRIMARY KEY,
            TenPhong NVARCHAR(100),
            TruongPhong INT,
            CONSTRAINT FK_PHONGBAN_NHANVIEN
              FOREIGN KEY (TruongPhong)
              REFERENCES NHANVIEN(MaNhanVien)
              ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        // Bảng CHUCVU
        "CREATE TABLE IF NOT EXISTS CHUCVU (
            MaChucVu INT PRIMARY KEY,
            TenChucVu NVARCHAR(100),
            TenRole NVARCHAR(100)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ];

    foreach ($stmts as $sql) {
        if (! $conn->query($sql)) {
            throw new Exception("Lỗi khi tạo bảng: " . $conn->error);
        }
    }
    echo "✓ Các bảng đã được khởi tạo thành công.<br>";

    // Danh sách ALTER TABLE để thêm FK (nếu chưa được tạo trong lệnh trên)
    $fks = [
        "ALTER TABLE NHANVIEN
           ADD CONSTRAINT FK_NHANVIEN_PHONGBAN
           FOREIGN KEY (MaPhong)
           REFERENCES PHONGBAN(MaPhong)
           ON DELETE SET NULL;",

        "ALTER TABLE NHANVIEN
           ADD CONSTRAINT FK_NHANVIEN_CHUCVU
           FOREIGN KEY (MaChucVu)
           REFERENCES CHUCVU(MaChucVu)
           ON DELETE SET NULL;"
    ];

    foreach ($fks as $sql) {
        // Có thể chạy fail nếu đã tồn tại constraint → nên wrap try
        if (! $conn->query($sql)) {
            // Nếu lỗi do tồn tại constraint thì bỏ qua, còn lỗi khác thì ném exception
            if (strpos($conn->error, 'Duplicate') === false) {
                throw new Exception("Lỗi thêm FK: " . $conn->error);
            }
        }
    }
    echo "✓ Các ràng buộc khóa ngoại đã được thêm thành công.<br>";

} catch (Exception $e) {
    echo "<p style='color:red;'>‼ " . $e->getMessage() . "</p>";
}

// 3) Đóng connection
$conn->close();
