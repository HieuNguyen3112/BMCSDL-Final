<?php
// database/seed_more.php

// 1) Nạp init để có $conn (mysqli)
require_once __DIR__ . '/init.php';

try {
    $errors = [];

    // —————————————————————————
    // 2) Danh sách nhân viên mới (2 user cho mỗi role 3,4,5,6)
    $employees = [
        // HoTen         GioiTinh NgaySinh     SDT         Luong      PhuCap     MaSoThue      MaChucVu MaPhong
        ['Lê Thị A',     'Nữ',    '1992-05-10','0901111111',15000000, 2000000,   '111111111',   3,      1],
        ['Phạm Văn B',   'Nam',   '1993-08-22','0902222222',15500000, 2100000,   '222222222',   3,      1],

        ['Trần Thị C',   'Nữ',    '1988-02-14','0903333333',20000000, 3000000,   '333333333',   4,      1],
        ['Đỗ Văn D',     'Nam',   '1987-11-03','0904444444',20500000, 3100000,   '444444444',   4,      1],

        ['Ngô Thị E',    'Nữ',    '1991-07-30','0905555555',16000000, 2200000,   '555555555',   5,      2],
        ['Vũ Văn F',     'Nam',   '1990-03-18','0906666666',16500000, 2300000,   '666666666',   5,      2],

        ['Bùi Thị G',    'Nữ',    '1980-12-01','0907777777',30000000, 5000000,   '777777777',   6,      3],
        ['Lưu Văn H',    'Nam',   '1979-09-09','0908888888',31000000, 5200000,   '888888888',   6,      3],
    ];

    // Build VALUES clause cho NHANVIEN
    $valuesNV = [];
    foreach ($employees as $e) {
        list($ht,$gt,$ns,$sdt,$luong,$pc,$mst,$cv,$pb) = $e;
        $valuesNV[] = sprintf(
            "('%s','%s','%s','%s',AES_ENCRYPT('%s','nhom6'),AES_ENCRYPT('%s','nhom6'),'%s',%d,%d)",
            $conn->real_escape_string($ht),
            $conn->real_escape_string($gt),
            $conn->real_escape_string($ns),
            $conn->real_escape_string($sdt),
            $conn->real_escape_string($luong),
            $conn->real_escape_string($pc),
            $conn->real_escape_string($mst),
            $cv,
            $pb
        );
    }
    $sqlNV = "INSERT INTO NHANVIEN
        (HoTen,GioiTinh,NgaySinh,SoDienThoai,Luong,PhuCap,MaSoThue,MaChucVu,MaPhong)
        VALUES " . implode(",", $valuesNV);

    if (! $conn->query($sqlNV)) {
        $errors[] = "NHANVIEN_MORE: " . $conn->error;
    } else {
        echo "✓ Seed NHANVIEN_MORE OK.<br>";
    }

    // —————————————————————————
    // 3) Lấy ID vừa chèn (8 bản ghi) để gán tài khoản
    $count = count($employees);
    $res = $conn->query("SELECT MaNhanVien FROM NHANVIEN ORDER BY MaNhanVien DESC LIMIT $count");
    if (! $res) {
        $errors[] = "FETCH_IDS: " . $conn->error;
    } else {
        $ids = [];
        while ($r = $res->fetch_assoc()) {
            $ids[] = (int)$r['MaNhanVien'];
        }

        // Build VALUES clause cho TAIKHOAN
        $valuesTK = array_map(function($id){
            return sprintf("(%d,UNHEX(SHA2('123456',512)))", $id);
        }, $ids);

        $sqlTK = "INSERT INTO TAIKHOAN (MaNhanVien, MatKhau) VALUES " . implode(",", $valuesTK);
        if (! $conn->query($sqlTK)) {
            $errors[] = "TAIKHOAN_MORE: " . $conn->error;
        } else {
            echo "✓ Seed TAIKHOAN_MORE OK.<br>";
        }
    }

    // —————————————————————————
    // 4) Kết quả
    if (count($errors)) {
        echo "<p style='color:red;'>Có lỗi khi seed_more:</p>";
        foreach ($errors as $e) {
            echo "- $e<br>";
        }
    } else {
        echo "<strong>✅ Seed_more hoàn tất thành công.</strong>";
    }

} catch (Exception $ex) {
    echo "<p style='color:red;'>Exception: " . $ex->getMessage() . "</p>";
}

$conn->close();
