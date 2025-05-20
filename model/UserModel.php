<?php
// model/UserModel.php

class UserModel
{
    /** @var mysqli */
    protected $conn;

    /**
     * @param mysqli $conn
     */
    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Xác thực đăng nhập, trả về mảng user hoặc null nếu không hợp lệ
     *
     * @param string $maNV
     * @param string $password
     * @return array|null
     */
    public function authenticate(string $maNV, string $password): ?array
    {
        $sql = "
            SELECT nv.MaNhanVien, nv.HoTen, nv.MaPhong, nv.MaChucVu
            FROM TAIKHOAN tk
            JOIN NHANVIEN nv ON nv.MaNhanVien = tk.MaNhanVien
            WHERE tk.MaNhanVien = ?
              AND tk.MatKhau = UNHEX(SHA2(?, 512))
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('is', $maNV, $password);
        $stmt->execute();
        $res  = $stmt->get_result();
        $user = $res->fetch_assoc();
        $stmt->close();

        return $user ?: null;
    }

    /**
     * Lấy thông tin 1 user theo id
     *
     * @param int $maNV
     * @return array|null
     */
    public function getById(int $maNV): ?array
    {
        $sql = "
            SELECT 
            nv.MaNhanVien,
            nv.HoTen,
            nv.GioiTinh,
            nv.NgaySinh,
            nv.SoDienThoai,
            nv.Luong,
            nv.PhuCap,
            nv.MaSoThue,
            cv.TenChucVu,    -- tên chức vụ
            pb.TenPhong      -- tên phòng ban
            FROM nhanvien nv
            LEFT JOIN chucvu    cv ON nv.MaChucVu  = cv.MaChucVu
            LEFT JOIN phongban pb ON nv.MaPhong    = pb.MaPhong
            WHERE nv.MaNhanVien = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $maNV);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    /**
     * Cập nhật thông tin cá nhân (chỉ 4 trường cơ bản)
     *
     * @param int   $maNV
     * @param array $data  ['HoTen','GioiTinh','NgaySinh','SoDienThoai']
     * @return bool
     */
    public function updatePersonal(int $maNV, array $data): bool
    {
        $sql = "
            UPDATE NHANVIEN
            SET HoTen       = ?,
                GioiTinh    = ?,
                NgaySinh    = ?,
                SoDienThoai = ?
            WHERE MaNhanVien = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'ssssi',
            $data['HoTen'],
            $data['GioiTinh'],
            $data['NgaySinh'],
            $data['SoDienThoai'],
            $maNV
        );
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }
}
