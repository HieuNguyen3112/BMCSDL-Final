<?php
// model/UserModel.php
class UserModel
{
    /** @var mysqli */
    protected $db;

    public function __construct(mysqli $conn)
    {
        $this->db = $conn;
    }

    /**
     * Kiểm tra login, trả về mảng thông tin user (nhân viên) hoặc `null` nếu sai.
     */
    public function authenticate(string $maNV, string $password): ?array
    {
        // hash mật khẩu như khi seed: SHA2_512('password' . MaNhanVien)
        $stmt = $this->db->prepare("
            SELECT nv.MaNhanVien, nv.HoTen, nv.MaPhong, nv.MaChucVu
            FROM TAIKHOAN tk
            INNER JOIN NHANVIEN nv ON tk.MaNhanVien = nv.MaNhanVien
            WHERE tk.MaNhanVien = ?
              AND tk.MatKhau = UNHEX(SHA2(CONCAT(?, ?), 512))
            LIMIT 1
        ");
        $stmt->bind_param('iss', $maNV, $password, $maNV);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        $stmt->close();

        return $user ?: null;
    }
}
