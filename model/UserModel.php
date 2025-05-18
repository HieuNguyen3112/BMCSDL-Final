<?php
// model/UserModel.php

class UserModel
{
    protected $db;

    public function __construct(mysqli $conn)
    {
        $this->db = $conn;
    }

    /**
     * Xác thực đăng nhập, trả về mảng user hoặc null nếu không hợp lệ
     */
    public function authenticate(string $maNV, string $password): ?array
    {
        // Chỉ hash mỗi password (khớp với seed UNHEX(SHA2('123456',512)))
        $stmt = $this->db->prepare("
            SELECT nv.MaNhanVien, nv.HoTen, nv.MaPhong, nv.MaChucVu
            FROM TAIKHOAN tk
            JOIN NHANVIEN nv ON nv.MaNhanVien = tk.MaNhanVien
            WHERE tk.MaNhanVien = ?
              AND tk.MatKhau = UNHEX(SHA2(?, 512))
            LIMIT 1
        ");
        // 'i' với maNV, 's' với password
        $stmt->bind_param('is', $maNV, $password);
        $stmt->execute();
        $res  = $stmt->get_result();
        $user = $res->fetch_assoc();
        $stmt->close();
        return $user ?: null;
    }
}
