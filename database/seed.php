<?php
// database/seed.php

// 1) Nạp init để có $conn (mysqli)
require_once __DIR__ . '/init.php';

try {
    $errors = [];

    // —————————————————————————
    // 2) Seed PHONGBAN
    $sql = "
      INSERT INTO PHONGBAN (TenPhong) VALUES
        ('Phòng nhân sự'),
        ('Phòng tài vụ'),
        ('Phòng giám đốc')
    ";
    if (! $conn->query($sql) && strpos($conn->error, 'Duplicate') === false) {
        $errors[] = "PHONGBAN: " . $conn->error;
    } else {
        echo "✓ Seed PHONGBAN OK.<br>";
    }

    // // 3) Seed CHUCVU
    $sql = "
      INSERT INTO CHUCVU (MaChucVu, TenChucVu, TenRole) VALUES
        (1, 'Nhân viên', 'NhanVienRole'),
        (2, 'Trưởng phòng', 'TruongPhongRole'),
        (3, 'Nhân viên phòng nhân sự', 'NhanVienNhanSuRole'),
        (4, 'Trưởng phòng nhân sự', 'TruongPhongNhanSuRole'),
        (5, 'Nhân viên phòng tài vụ', 'NhanVienTaiVuRole'),
        (6, 'Giám đốc', 'GiamDocRole')
    ";
    if (! $conn->query($sql) && strpos($conn->error, 'Duplicate') === false) {
        $errors[] = "CHUCVU: " . $conn->error;
    } else {
        echo "✓ Seed CHUCVU OK.<br>";
    }

    // —————————————————————————
    // 4) Seed NHANVIEN
    $sql = "
      INSERT INTO NHANVIEN 
        (HoTen, GioiTinh, NgaySinh, SoDienThoai, Luong, PhuCap, MaSoThue, MaChucVu, MaPhong)
      VALUES
        ('Nguyễn Minh Hiếu', 'Nam', '2004-12-31', '0123456789',
          '19000000', '2400000', '123456789', 1, 1),
        ('Trần Đình Trọng', 'Nam', '1998-03-27', '0987654321',
          '21000000', '2500000', '8563489385', 2, 2),
        ('Đoàn Văn Hậu',    'Nam', '1999-03-12', '0912345678',
          '18500000', '2200000', '9638284638', 1, 1),
        ('Hồ Xuân Nga',     'Nữ',  '1990-02-01', '0901234567',
          '20000000', '2300000', '3483748774', 1, 3)
    ";
    if (! $conn->query($sql)) {
        $errors[] = "NHANVIEN: " . $conn->error;
    } else {
        echo "✓ Seed NHANVIEN OK.<br>";
    }

    // 5) Seed TAIKHOAN dựa trên MaNhanVien vừa tạo (1–4)
    //    mật khẩu sẽ là UNHEX(SHA2('password'+id,512))
    $sql = "
        INSERT INTO TAIKHOAN (MaNhanVien, MatKhau) VALUES
        (1, UNHEX(SHA2('123456',512))),
        (2, UNHEX(SHA2('123456',512))),
        (3, UNHEX(SHA2('123456',512))),
        (4, UNHEX(SHA2('123456',512)))
    ";
    if (! $conn->query($sql)) {
        $errors[] = "TAIKHOAN: " . $conn->error;
    } else {
        echo "✓ Seed TAIKHOAN OK.<br>";
    }

    // —————————————————————————
    // 6) Báo kết quả
    if (count($errors)) {
        echo "<p style='color:red;'>Có lỗi khi seed:</p>";
        foreach ($errors as $err) {
            echo "- $err<br>";
        }
    } else {
        echo "<strong>✅ Seed NHÂN VIÊN & TÀI KHOẢN hoàn tất.</strong>";
    }

} catch (Exception $e) {
    echo "<p style='color:red;'>Exception: " . $e->getMessage() . "</p>";
}

// 7) Đóng kết nối
$conn->close();
