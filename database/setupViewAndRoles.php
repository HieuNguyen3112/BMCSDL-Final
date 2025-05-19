<?php
// File: database/setupViewAndRoles.php

// 1) Nạp init để có $conn (mysqli)
require_once __DIR__ . '/init.php';

$errors = [];

// —————————————————————————
// 2) Tạo View view_NHANVIEN_NhanVienRole
$sqlView = <<<SQL
DROP VIEW IF EXISTS view_NHANVIEN_NhanVienRole;
CREATE VIEW view_NHANVIEN_NhanVienRole AS
SELECT 
  NV.MaNhanVien,
  NV.HoTen,
  NV.GioiTinh,
  NV.NgaySinh,
  NV.SoDienThoai,
  NV.MaSoThue,
  CV.TenChucVu,
  PB.TenPhong
FROM NHANVIEN NV
JOIN CHUCVU CV ON NV.MaChucVu = CV.MaChucVu
JOIN PHONGBAN PB ON NV.MaPhong = PB.MaPhong
WHERE PB.MaPhong = (
  SELECT MaPhong 
  FROM NHANVIEN 
  WHERE MaNhanVien = CAST(SUBSTRING_INDEX(CURRENT_USER(),'@',1) AS UNSIGNED)
);
SQL;

if (! $conn->multi_query($sqlView)) {
    $errors[] = 'VIEW: ' . $conn->error;
}
while ($conn->more_results()) { $conn->next_result(); }
if (empty($errors)) echo "✓ View view_NHANVIEN_NhanVienRole created.<br>";

// —————————————————————————
// 3) Tạo Roles & gán quyền
$sqlRoles = <<<SQL
-- Role NhanVienRole
CREATE ROLE IF NOT EXISTS NhanVienRole;
GRANT SELECT (MaNhanVien,HoTen,GioiTinh,NgaySinh,SoDienThoai,MaSoThue,MaChucVu,MaPhong)
  ON NHANVIEN TO NhanVienRole;
GRANT SELECT ON CHUCVU, PHONGBAN, TAIKHOAN, view_NHANVIEN_NhanVienRole TO NhanVienRole;

-- Role TruongPhongRole
CREATE ROLE IF NOT EXISTS TruongPhongRole;
GRANT SELECT ON NHANVIEN,CHUCVU,PHONGBAN,TAIKHOAN TO TruongPhongRole;
GRANT EXECUTE ON SP_SEL_NHANVIEN_TruongPhongRole TO TruongPhongRole;

-- Role GiamDocRole
CREATE ROLE IF NOT EXISTS GiamDocRole;
GRANT SELECT ON NHANVIEN,CHUCVU,PHONGBAN,TAIKHOAN TO GiamDocRole;
GRANT UPDATE (Luong,PhuCap) ON NHANVIEN TO GiamDocRole;

-- Role LoginRole
CREATE ROLE IF NOT EXISTS LoginRole;
GRANT SELECT ON TAIKHOAN TO LoginRole;

-- Role NhanVienNhanSuRole (nhân viên phòng nhân sự)
CREATE ROLE IF NOT EXISTS NhanVienNhanSuRole;
GRANT SELECT ON NHANVIEN TO NhanVienNhanSuRole;

-- Role TruongPhongNhanSuRole (trưởng phòng nhân sự)
CREATE ROLE IF NOT EXISTS TruongPhongNhanSuRole;
GRANT SELECT, INSERT, UPDATE ON NHANVIEN TO TruongPhongNhanSuRole;

-- Role NhanVienTaiVuRole (nhân viên phòng tài vụ)
CREATE ROLE IF NOT EXISTS NhanVienTaiVuRole;
GRANT SELECT ON NHANVIEN TO NhanVienTaiVuRole;
SQL;

if (! $conn->multi_query($sqlRoles)) {
    $errors[] = 'ROLES: ' . $conn->error;
}
while ($conn->more_results()) { $conn->next_result(); }
if (empty($errors)) echo "✓ All Roles & privileges set.<br>";

// —————————————————————————
// 4) Tạo user QLNS_Login và gán roles
$sqlUser = <<<SQL
CREATE USER IF NOT EXISTS 'QLNS_Login'@'%' IDENTIFIED BY 'QLNS_Login';
GRANT NhanVienRole,
      TruongPhongRole,
      GiamDocRole,
      LoginRole,
      NhanVienNhanSuRole,
      TruongPhongNhanSuRole,
      NhanVienTaiVuRole
  TO 'QLNS_Login'@'%';
SQL;

if (! $conn->multi_query($sqlUser)) {
    $errors[] = 'USER QLNS_Login: ' . $conn->error;
}
while ($conn->more_results()) { $conn->next_result(); }
if (empty($errors)) echo "✓ User QLNS_Login created & roles assigned.<br>";

// —————————————————————————
// 5) Báo lỗi (nếu có)
if (count($errors)) {
    echo "<p style='color:red;'>Có lỗi trong quá trình thiết lập:</p>";
    foreach ($errors as $e) {
        echo "- {$e}<br>";
    }
} else {
    echo "<strong>✅ Toàn bộ View, Roles và User QLNS_Login đã được tạo/thay đổi thành công.</strong>";
}

// 6) Đóng kết nối
$conn->close();
