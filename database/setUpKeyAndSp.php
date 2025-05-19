<?php
// File: database/setUpKeyAndSp.php

// 1) Nạp init để có $conn (mysqli)
require_once __DIR__ . '/init.php';

try {
    $errors = [];

    // —————————————————————————
    // 2) Tạo SP_IN_NHANVIEN
    $sql = "
      DROP PROCEDURE IF EXISTS SP_IN_NHANVIEN;
      CREATE PROCEDURE SP_IN_NHANVIEN(
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
            AES_ENCRYPT(p_Luong,  'nhom6'),
            AES_ENCRYPT(p_PhuCap, 'nhom6'),
            p_MaSoThue,
            p_MaChucVu,
            p_MaPhong
          );
      END;
    ";
    if (! $conn->multi_query($sql)) {
        $errors[] = 'SP_IN_NHANVIEN: ' . $conn->error;
    }
    // xả hết các result sets còn dư
    while ($conn->more_results() && $conn->next_result()) {;}

    // —————————————————————————
    // 3) Tạo SP_SEL_NHANVIEN_TruongPhongRole
    $sql = "
      DROP PROCEDURE IF EXISTS SP_SEL_NHANVIEN_TruongPhongRole;
      CREATE PROCEDURE SP_SEL_NHANVIEN_TruongPhongRole(
        IN p_MaNhanVien INT
      )
      BEGIN
        SELECT 
          NV.MaNhanVien,
          NV.HoTen,
          NV.GioiTinh,
          NV.NgaySinh,
          NV.SoDienThoai,
          CAST(AES_DECRYPT(NV.Luong,  'nhom6') AS DECIMAL(15,2)) AS Luong,
          CAST(AES_DECRYPT(NV.PhuCap, 'nhom6') AS DECIMAL(15,2)) AS PhuCap,
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
      END;
    ";
    if (! $conn->multi_query($sql)) {
        $errors[] = 'SP_SEL_NHANVIEN_TruongPhongRole: ' . $conn->error;
    }
    while ($conn->more_results() && $conn->next_result()) {;}

    // —————————————————————————
    // 4) Tạo SP_UPD_NHANVIEN_TruongPhongRole
    $sql = "
      DROP PROCEDURE IF EXISTS SP_UPD_NHANVIEN_TruongPhongRole;
      CREATE PROCEDURE SP_UPD_NHANVIEN_TruongPhongRole(
        IN p_MaNhanVien INT,
        IN p_Luong       DECIMAL(15,2),
        IN p_PhuCap      DECIMAL(15,2)
      )
      BEGIN
        UPDATE NHANVIEN
        SET 
          Luong  = AES_ENCRYPT(p_Luong,  'nhom6'),
          PhuCap = AES_ENCRYPT(p_PhuCap, 'nhom6')
        WHERE MaNhanVien = p_MaNhanVien;
      END;
    ";
    if (! $conn->multi_query($sql)) {
        $errors[] = 'SP_UPD_NHANVIEN_TruongPhongRole: ' . $conn->error;
    }
    while ($conn->more_results() && $conn->next_result()) {;}

    // —————————————————————————
    // 5) Tạo SP_SEL_DANGNHAP
    $sql = "
      DROP PROCEDURE IF EXISTS SP_SEL_DANGNHAP;
      CREATE PROCEDURE SP_SEL_DANGNHAP(
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
      END;
    ";
    if (! $conn->multi_query($sql)) {
        $errors[] = 'SP_SEL_DANGNHAP: ' . $conn->error;
    }
    while ($conn->more_results() && $conn->next_result()) {;}

    // —————————————————————————
    // 6) Báo kết quả
    if (count($errors) > 0) {
        echo "<p style='color:red;'>Có lỗi khi tạo store procedures:</p>";
        foreach ($errors as $e) {
            echo "- {$e}<br>";
        }
    } else {
        echo "<strong>✅ Đã tạo/đổi thành công các stored procedures:</strong><br>";
        echo "&bull; SP_IN_NHANVIEN<br>";
        echo "&bull; SP_SEL_NHANVIEN_TruongPhongRole<br>";
        echo "&bull; SP_UPD_NHANVIEN_TruongPhongRole<br>";
        echo "&bull; SP_SEL_DANGNHAP<br>";
    }

} catch (Exception $ex) {
    echo "<p style='color:red;'>Exception: " 
         . $ex->getMessage() . "</p>";
}

// 7) Đóng kết nối
$conn->close();
