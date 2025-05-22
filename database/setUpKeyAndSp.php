<?php
// File: database/setUpKeyAndSp.php

// 1) Nạp init để có $conn (mysqli)
require_once __DIR__ . '/init.php';

// Khóa AES cố định
$key = 'nhom6';

$errors = [];

// Các procedure cần tạo
$procedures = [

    // 1) SP_IN_NHANVIEN
    "DROP PROCEDURE IF EXISTS SP_IN_NHANVIEN;",
    "CREATE PROCEDURE SP_IN_NHANVIEN(
        IN p_HoTen        VARCHAR(100),
        IN p_GioiTinh     VARCHAR(3),
        IN p_NgaySinh     DATE,
        IN p_SoDienThoai  VARCHAR(15),
        IN p_Luong        DECIMAL(15,2),
        IN p_PhuCap       DECIMAL(15,2),
        IN p_MaSoThue     VARCHAR(20),
        IN p_MaChucVu     INT,
        IN p_MaPhong      INT
     )
     BEGIN
       INSERT INTO NHANVIEN
         (HoTen, GioiTinh, NgaySinh, SoDienThoai, Luong, PhuCap, MaSoThue, MaChucVu, MaPhong)
       VALUES
         (
           p_HoTen,
           p_GioiTinh,
           p_NgaySinh,
           p_SoDienThoai,
           AES_ENCRYPT(p_Luong,  '{$key}'),
           AES_ENCRYPT(p_PhuCap, '{$key}'),
           p_MaSoThue,
           p_MaChucVu,
           p_MaPhong
         );
     END;",

    // 2) SP_SEL_NHANVIEN_TruongPhongRole
    "DROP PROCEDURE IF EXISTS SP_SEL_NHANVIEN_TruongPhongRole;",
    "CREATE PROCEDURE SP_SEL_NHANVIEN_TruongPhongRole(
        IN p_MaNhanVien INT
     )
     BEGIN
       SELECT 
         NV.MaNhanVien,
         NV.HoTen,
         NV.GioiTinh,
         DATE_FORMAT(NV.NgaySinh, '%d/%m/%Y') AS NgaySinh,
         NV.SoDienThoai,
         CAST(AES_DECRYPT(NV.Luong,  '{$key}') AS CHAR(20)) AS Luong,
         CAST(AES_DECRYPT(NV.PhuCap, '{$key}') AS CHAR(20)) AS PhuCap,
         NV.MaSoThue,
         CV.TenChucVu,
         PB.TenPhong
       FROM NHANVIEN NV
       JOIN CHUCVU CV ON NV.MaChucVu = CV.MaChucVu
       JOIN PHONGBAN PB ON NV.MaPhong = PB.MaPhong
       WHERE PB.MaPhong = (
         SELECT MaPhong 
         FROM NHANVIEN 
         WHERE MaNhanVien = p_MaNhanVien
       );
     END;",

    // 3) SP_UPD_NHANVIEN_TruongPhongRole
    "DROP PROCEDURE IF EXISTS SP_UPD_NHANVIEN_TruongPhongRole;",
    "CREATE PROCEDURE SP_UPD_NHANVIEN_TruongPhongRole(
        IN p_MaNhanVien INT,
        IN p_Luong       DECIMAL(15,2),
        IN p_PhuCap      DECIMAL(15,2)
     )
     BEGIN
       UPDATE NHANVIEN
         SET 
           Luong  = AES_ENCRYPT(p_Luong,  '{$key}'),
           PhuCap = AES_ENCRYPT(p_PhuCap, '{$key}')
       WHERE MaNhanVien = p_MaNhanVien;
     END;",

    // 4) SP_SEL_DANGNHAP
    "DROP PROCEDURE IF EXISTS SP_SEL_DANGNHAP;",
    "CREATE PROCEDURE SP_SEL_DANGNHAP(
        IN p_MaNhanVien VARCHAR(10),
        IN p_MatKhau    VARCHAR(50)
     )
     BEGIN
       DECLARE v_TruyVan  VARCHAR(20);
       DECLARE v_ThongBao VARCHAR(255);

       IF EXISTS(
         SELECT 1 FROM TAIKHOAN
         WHERE MaNhanVien = p_MaNhanVien
           AND MatKhau = UNHEX(SHA2(p_MatKhau,512))
       ) THEN
         SET v_TruyVan  = 'ThanhCong';
         SET v_ThongBao = p_MaNhanVien;
       ELSE
         SET v_TruyVan  = 'ThatBai';
         SET v_ThongBao = 'Sai tài khoản hoặc mật khẩu';
       END IF;

       SELECT v_TruyVan AS TruyVan, v_ThongBao AS ThongBao;
     END;"
];

// 2) Tạo / ghi đè SP
foreach ($procedures as $sql) {
    if (! $conn->multi_query($sql)) {
        $errors[] = $conn->error;
    }
    // xả hết các result sets
    while ($conn->more_results() && $conn->next_result()) {;}
}

// 3) Tái-mã-hoá dữ liệu cũ (nếu cột Luong/PhuCap đang lưu plain-text)
if (empty($errors)) {
    $reenc = "
      UPDATE NHANVIEN
      SET
        Luong  = AES_ENCRYPT(CAST(Luong AS CHAR), '{$key}'),
        PhuCap = AES_ENCRYPT(CAST(PhuCap AS CHAR), '{$key}')
    ";
    if (! $conn->query($reenc)) {
        $errors[] = 'Re-encrypt NHANVIEN: ' . $conn->error;
    }
}

// 4) Xuất kết quả
header('Content-Type: text/html; charset=utf-8');
if (count($errors)) {
    echo "<h3 style='color:red;'>✗ Có lỗi khi thiết lập:</h3><ul style='color:red;'>";
    foreach ($errors as $e) {
        echo '<li>' . htmlspecialchars($e) . '</li>';
    }
    echo "</ul>";
} else {
    echo "<h3 style='color:green;'>✓ Đã tạo thành công các Stored Procedures và tái-mã-hóa dữ liệu.</h3>
          <ul>
            <li>SP_IN_NHANVIEN</li>
            <li>SP_SEL_NHANVIEN_TruongPhongRole</li>
            <li>SP_UPD_NHANVIEN_TruongPhongRole</li>
            <li>SP_SEL_DANGNHAP</li>
          </ul>";
}

// 5) Đóng kết nối
$conn->close();
