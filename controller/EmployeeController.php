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
     * - Role 1 (NhanVienRole)   → SELECT từ view_NHANVIEN_NhanVienRole
     * - Role 2 (TruongPhongRole)→ CALL SP_SEL_NHANVIEN_TruongPhongRole()
     * - Role 6 (GiamDocRole)    → OPEN SYMMETRIC KEY và SELECT decrypt toàn bộ
     */
    public function apiListRoleNhanVien()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['success'=>false,'error'=>'Chưa đăng nhập'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $meId   = (int) $_SESSION['user']['MaNhanVien'];
        $myRole = (int) $_SESSION['user']['MaChucVu'];

        // Lấy MaPhong của user để dùng cho các case
        $deptStmt = $this->db->prepare(
            "SELECT MaPhong FROM NHANVIEN WHERE MaNhanVien = ?"
        );
        $deptStmt->bind_param("i", $meId);
        $deptStmt->execute();
        $myDept = (int)$deptStmt->get_result()->fetch_assoc()['MaPhong'];
        $deptStmt->close();

        try {
            if ($myRole === 1) {
                // Nhân viên thường: chỉ decrypt lương/phụ cấp của chính họ trong phòng
                $sql = "
                    SELECT 
                    nv.MaNhanVien,
                    nv.HoTen,
                    nv.GioiTinh,
                    DATE_FORMAT(nv.NgaySinh,'%d/%m/%Y') AS NgaySinh,
                    nv.SoDienThoai,
                    CASE WHEN nv.MaNhanVien = ?
                        THEN CAST(AES_DECRYPT(nv.Luong,'nhom6') AS CHAR(20))
                        ELSE NULL END AS Luong,
                    CASE WHEN nv.MaNhanVien = ?
                        THEN CAST(AES_DECRYPT(nv.PhuCap,'nhom6') AS CHAR(20))
                        ELSE NULL END AS PhuCap,
                    nv.MaSoThue,
                    cv.TenChucVu,
                    pb.TenPhong
                    FROM NHANVIEN nv
                    JOIN CHUCVU cv ON nv.MaChucVu = cv.MaChucVu
                    JOIN PHONGBAN pb ON nv.MaPhong = pb.MaPhong
                    WHERE nv.MaPhong = ?
                    ORDER BY nv.MaNhanVien
                ";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param("iii", $meId, $meId, $myDept);

            } elseif ($myRole === 2) {
                // Trưởng phòng: decrypt tất cả trong phòng mình
                $sql = "
                    SELECT 
                    nv.MaNhanVien,
                    nv.HoTen,
                    nv.GioiTinh,
                    DATE_FORMAT(nv.NgaySinh,'%d/%m/%Y') AS NgaySinh,
                    nv.SoDienThoai,
                    CAST(AES_DECRYPT(nv.Luong,'nhom6') AS CHAR(20)) AS Luong,
                    CAST(AES_DECRYPT(nv.PhuCap,'nhom6') AS CHAR(20)) AS PhuCap,
                    nv.MaSoThue,
                    cv.TenChucVu,
                    pb.TenPhong
                    FROM NHANVIEN nv
                    JOIN CHUCVU cv ON nv.MaChucVu = cv.MaChucVu
                    JOIN PHONGBAN pb ON nv.MaPhong = pb.MaPhong
                    WHERE nv.MaPhong = ?
                    ORDER BY nv.MaNhanVien
                ";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param("i", $myDept);

            } elseif (in_array($myRole, [5, 6], true)) {
                // Nhân viên phòng tài vụ (5) & Giám đốc (6): decrypt toàn bộ
                $sql = "
                    SELECT 
                    nv.MaNhanVien,
                    nv.HoTen,
                    nv.GioiTinh,
                    DATE_FORMAT(nv.NgaySinh,'%d/%m/%Y') AS NgaySinh,
                    nv.SoDienThoai,
                    CAST(AES_DECRYPT(nv.Luong,'nhom6') AS CHAR(20)) AS Luong,
                    CAST(AES_DECRYPT(nv.PhuCap,'nhom6') AS CHAR(20)) AS PhuCap,
                    nv.MaSoThue,
                    cv.TenChucVu,
                    pb.TenPhong
                    FROM NHANVIEN nv
                    JOIN CHUCVU cv ON nv.MaChucVu = cv.MaChucVu
                    JOIN PHONGBAN pb ON nv.MaPhong = pb.MaPhong
                    ORDER BY nv.MaNhanVien
                ";
                $stmt = $this->db->prepare($sql);

            } else {
                // Các role khác: không hiển thị lương/phụ cấp
                $sql = "
                    SELECT 
                    nv.MaNhanVien,
                    nv.HoTen,
                    nv.GioiTinh,
                    DATE_FORMAT(nv.NgaySinh,'%d/%m/%Y') AS NgaySinh,
                    nv.SoDienThoai,
                    NULL AS Luong,
                    NULL AS PhuCap,
                    nv.MaSoThue,
                    cv.TenChucVu,
                    pb.TenPhong
                    FROM NHANVIEN nv
                    JOIN CHUCVU cv ON nv.MaChucVu = cv.MaChucVu
                    JOIN PHONGBAN pb ON nv.MaPhong = pb.MaPhong
                    WHERE nv.MaChucVu IN (1,2)
                    ORDER BY nv.MaNhanVien
                ";
                $stmt = $this->db->prepare($sql);
            }

            // Thực thi và trả về data
            $stmt->execute();
            $res  = $stmt->get_result();
            $data = [];
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
            $stmt->close();

            echo json_encode(['success'=>true,'data'=>$data], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
            'success'=>false,
            'message'=>$e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }


    public function apiUpdate()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Chưa đăng nhập']);
            return;
        }

        $json = file_get_contents('php://input');
        $body = json_decode($json, true);

        // Validate dữ liệu đầu vào
        $requiredFields = ['MaNhanVien', 'HoTen', 'GioiTinh', 'NgaySinh', 'SoDienThoai', 'Luong', 'PhuCap', 'MaSoThue'];
        foreach ($requiredFields as $field) {
            if (empty($body[$field])) {
                echo json_encode(['success' => false, 'error' => "Thiếu trường $field"]);
                return;
            }
        }

        $maNV     = (int) $body['MaNhanVien'];
        $hoTen    = $body['HoTen'];
        $gioiTinh = $body['GioiTinh'];
        $ngaySinh = $body['NgaySinh'];
        $sdt      = $body['SoDienThoai'];
        $luong    = $body['Luong'];
        $phuCap   = $body['PhuCap'];
        $maSoThue = $body['MaSoThue'];

        // Nếu user là Nhân viên bình thường thì không cho phép sửa người khác
        if ($_SESSION['user']['MaChucVu'] === 1 && $_SESSION['user']['MaNhanVien'] != $maNV) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Không được chỉnh sửa người khác']);
            return;
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE NHANVIEN 
                SET HoTen=?, GioiTinh=?, NgaySinh=?, SoDienThoai=?, 
                    Luong=AES_ENCRYPT(?, 'nhom6'), 
                    PhuCap=AES_ENCRYPT(?, 'nhom6'), 
                    MaSoThue=? 
                WHERE MaNhanVien=?;
            ");
            $stmt->bind_param("sssssssi", $hoTen, $gioiTinh, $ngaySinh, $sdt, $luong, $phuCap, $maSoThue, $maNV);
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

        /**
     * API: POST /api/employees/create
     * Chỉ cho phép các role ≠ 1 (Nhân viên thường) thực hiện.
     */
    public function apiCreate()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Chưa đăng nhập']);
            return;
        }

        $myRole = (int) $_SESSION['user']['MaChucVu'];
        if ($myRole === 1) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Không có quyền thực hiện hành động này']);
            return;
        }

        $json = file_get_contents('php://input');
        $body = json_decode($json, true);

        // Validate dữ liệu đầu vào
        $required = ['HoTen','GioiTinh','NgaySinh','SoDienThoai','Luong','PhuCap','MaSoThue','MaChucVu','MaPhong'];
        foreach ($required as $f) {
            if (!isset($body[$f]) || $body[$f] === '') {
                echo json_encode(['success' => false, 'error' => "Thiếu trường $f"]);
                return;
            }
        }

        $hoTen     = $body['HoTen'];
        $gioiTinh  = $body['GioiTinh'];
        $ngaySinh  = $body['NgaySinh'];
        $sdt       = $body['SoDienThoai'];
        $luong     = $body['Luong'];
        $phuCap    = $body['PhuCap'];
        $maSoThue  = $body['MaSoThue'];
        $maChucVu  = (int) $body['MaChucVu'];
        $maPhong   = (int) $body['MaPhong'];

        try {
            $stmt = $this->db->prepare("
                INSERT INTO NHANVIEN
                  (HoTen, GioiTinh, NgaySinh, SoDienThoai,
                   Luong, PhuCap, MaSoThue, MaChucVu, MaPhong)
                VALUES
                  (?, ?, ?, ?,
                   AES_ENCRYPT(?, 'nhom6'),
                   AES_ENCRYPT(?, 'nhom6'),
                   ?, ?, ?)
            ");
            // 7 x string + 2 x int
            $stmt->bind_param(
              'sssssssii',
              $hoTen, $gioiTinh, $ngaySinh, $sdt,
              $luong, $phuCap,
              $maSoThue, $maChucVu, $maPhong
            );
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'Tạo nhân viên thành công']);
        }
        catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

}
