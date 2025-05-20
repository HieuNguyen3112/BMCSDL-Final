<?php
// controller/EmployeeController.php
require_once __DIR__ . '/../model/UserModel.php';

class EmployeeController
{
    /** @var mysqli */
    protected $db;

    /** @var UserModel */
    protected $userModel;

    public function __construct(mysqli $conn)
    {
        $this->db        = $conn;
        $this->userModel = new UserModel($conn);

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Hiển thị trang danh sách nhân viên
     */
    public function show()
    {
        if (empty($_SESSION['user'])) {
            header('Location: /phpcoban/BMCSDL-Final/signin.php');
            exit;
        }

        require __DIR__ . '/../employees.php';
    }

    /**
     * API: GET /api/employees/nhanvien
     * Trả về JSON danh sách MaChucVu IN (1,2) — Nhân viên & Trưởng phòng
     * Với role Nhân viên (1): chỉ xem salary/phucap của bản thân
     */
    public function apiListRoleNhanVien()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error'   => 'Chưa đăng nhập'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $meId   = (int) $_SESSION['user']['MaNhanVien'];
        $myRole = (int) $_SESSION['user']['MaChucVu'];

        // Nếu là role Nhân viên => ẩn salary/phu cap của người khác
        if ($myRole === 1) {
            $salaryExpr    = "CASE WHEN nv.MaNhanVien = {$meId} THEN nv.Luong ELSE NULL END AS Luong";
            $allowanceExpr = "CASE WHEN nv.MaNhanVien = {$meId} THEN nv.PhuCap ELSE NULL END AS PhuCap";
        } else {
            // Trưởng phòng (2) hoặc các role cao hơn: xem đầy đủ
            $salaryExpr    = "nv.Luong";
            $allowanceExpr = "nv.PhuCap";
        }

        $sql = "
            SELECT 
                nv.MaNhanVien,
                nv.HoTen,
                nv.GioiTinh,
                DATE_FORMAT(nv.NgaySinh, '%d/%m/%Y') AS NgaySinh,
                nv.SoDienThoai,
                {$salaryExpr},
                {$allowanceExpr},
                nv.MaSoThue,
                cv.TenChucVu,
                pb.TenPhong
            FROM NHANVIEN nv
            JOIN CHUCVU    cv ON nv.MaChucVu = cv.MaChucVu
            JOIN PHONGBAN  pb ON nv.MaPhong  = pb.MaPhong
            WHERE nv.MaChucVu IN (1,2)
            ORDER BY nv.MaNhanVien
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $res = $stmt->get_result();

        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        echo json_encode([
            'success' => true,
            'data'    => $rows
        ], JSON_UNESCAPED_UNICODE);
    }
}
