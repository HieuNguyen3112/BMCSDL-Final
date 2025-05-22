<?php
// test.php

// 1) Lấy cấu hình database
$config = require __DIR__ . '/database/config.php';

// 2) Kết nối
$host   = $config['host'];
$user   = $config['username'];
$pass   = $config['password'];
$dbname = $config['dbname'];
$charset= $config['charset'];

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset($charset);

// 3) Chọn MaNhanVien muốn thử
$testId = 1;

// 4) Lấy bản mã hoá & giải mã từ bảng NHANVIEN
$sql = "
    SELECT 
      Luong                     AS encrypted_luong,
      AES_DECRYPT(Luong, 'nhom6')  AS decrypted_luong,
      PhuCap                    AS encrypted_phucap,
      AES_DECRYPT(PhuCap, 'nhom6') AS decrypted_phucap
    FROM NHANVIEN
    WHERE MaNhanVien = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $testId);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

// 5) Hiển thị kết quả
header('Content-Type: text/plain; charset=utf-8');
echo "=== KIỂM TRA MÃ HÓA / GIẢI MÃ ===\n";
echo "Nhân viên #{$testId}\n\n";

echo "Luong (raw blob hex):\n";
echo bin2hex($res['encrypted_luong']) . "\n\n";

echo "Luong (AES_DECRYPT):\n";
echo $res['decrypted_luong'] . "\n\n";

echo "PhuCap (raw blob hex):\n";
echo bin2hex($res['encrypted_phucap']) . "\n\n";

echo "PhuCap (AES_DECRYPT):\n";
echo $res['decrypted_phucap'] . "\n\n";

// 6) Thử encrypt/decrypt trực tiếp trong query
$plainTest = 123456.78;
$sql2 = "
    SELECT 
      AES_ENCRYPT(?, 'nhom6')             AS blob_encrypted,
      AES_DECRYPT(AES_ENCRYPT(?, 'nhom6'), 'nhom6') AS blob_decrypted
";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("dd", $plainTest, $plainTest);
$stmt2->execute();
$r2 = $stmt2->get_result()->fetch_assoc();

echo "=== KIỂM TRA ENCRYPT/DECRYPT NGAY TRONG QUERY ===\n";
echo "Plain value: $plainTest\n";
echo "Encrypted blob (hex): " . bin2hex($r2['blob_encrypted']) . "\n";
echo "Decrypted back   : " . $r2['blob_decrypted'] . "\n";

$stmt->close();
$stmt2->close();
$conn->close();
