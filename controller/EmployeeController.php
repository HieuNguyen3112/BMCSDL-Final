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
     * Quyền xem:
     * - NhanVienRole (1): xem data trong phòng mình, chỉ Lương/Phụ cấp của chính họ
     * - TruongPhongRole (2): xem tất cả nhân viên trong phòng mình, không edit
     * - NhanVienNhanSuRole (4): xem, thêm, edit nhân viên **tất cả phòng** (trừ phòng nhân sự)
     * - TruongPhongNhanSuRole (??): xem/edit tất cả, (trừ Lương/Phụ cấp chính họ)
     * - NhanVienTaiVuRole (5): xem tất cả trong phòng mình (có cả L/P), xem MãNV/L/P/MST của phòng khác
     * - GiamDocRole (6): xem tất cả, chỉ edit L/P
     */
    public function apiListRoleNhanVien()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['success'=>false,'error'=>'Chưa đăng nhập'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $meId       = (int) $_SESSION['user']['MaNhanVien'];
        $myRole     = (int) $_SESSION['user']['MaChucVu'];
        $myRoleName = $_SESSION['user']['TenRole'];
        // lấy phòng của chính họ
        $stmt = $this->db->prepare("SELECT MaPhong FROM NHANVIEN WHERE MaNhanVien=?");
        $stmt->bind_param("i",$meId);
        $stmt->execute();
        $dept = (int)$stmt->get_result()->fetch_assoc()['MaPhong'];
        $stmt->close();

        // Build SQL tuỳ quyền
        // Chuẩn bị câu lệnh SQL và tham số
        try {
            if ($myRole === 1) { // Nhân viên
                $sql = "
                SELECT nv.MaNhanVien,nv.HoTen,nv.GioiTinh,
                        DATE_FORMAT(nv.NgaySinh,'%d/%m/%Y') AS NgaySinh,
                        nv.SoDienThoai,
                        CASE WHEN nv.MaNhanVien=? THEN CAST(AES_DECRYPT(nv.Luong,'nhom6') AS CHAR(20)) ELSE '********' END AS Luong,
                        CASE WHEN nv.MaNhanVien=? THEN CAST(AES_DECRYPT(nv.PhuCap,'nhom6') AS CHAR(20)) ELSE '********' END AS PhuCap,
                        nv.MaSoThue,cv.TenChucVu,pb.TenPhong
                FROM NHANVIEN nv
                JOIN CHUCVU cv ON nv.MaChucVu=cv.MaChucVu
                JOIN PHONGBAN pb ON nv.MaPhong=pb.MaPhong
                WHERE nv.MaPhong=? ORDER BY nv.MaNhanVien
                ";
                $p = [$meId, $meId, $dept];
            }

            elseif ($myRole === 2) { // Trưởng phòng (trừ nhân sự)
                $sql = "
                SELECT nv.MaNhanVien,nv.HoTen,nv.GioiTinh,
                        DATE_FORMAT(nv.NgaySinh,'%d/%m/%Y') AS NgaySinh,
                        nv.SoDienThoai,
                        CASE WHEN nv.MaNhanVien=? THEN CAST(AES_DECRYPT(nv.Luong,'nhom6') AS CHAR(20)) ELSE NULL END AS Luong,
                        CASE WHEN nv.MaNhanVien=? THEN CAST(AES_DECRYPT(nv.PhuCap,'nhom6') AS CHAR(20)) ELSE NULL END AS PhuCap,
                        nv.MaSoThue,cv.TenChucVu,pb.TenPhong
                FROM NHANVIEN nv
                JOIN CHUCVU cv ON nv.MaChucVu=cv.MaChucVu
                JOIN PHONGBAN pb ON nv.MaPhong=pb.MaPhong
                WHERE nv.MaPhong=? ORDER BY nv.MaNhanVien
                ";
                $p = [$meId, $meId, $dept];
            }

            elseif ($myRoleName === 'NhanVienNhanSuRole') {
                // Xem tất cả nhân viên trừ người cùng phòng nhân sự
                $sql = "
                  SELECT nv.MaNhanVien,nv.HoTen,nv.GioiTinh,
                         DATE_FORMAT(nv.NgaySinh,'%d/%m/%Y') AS NgaySinh,
                         nv.SoDienThoai,
                         NULL AS Luong,NULL AS PhuCap,
                         nv.MaSoThue,cv.TenChucVu,pb.TenPhong
                  FROM NHANVIEN nv
                  JOIN CHUCVU cv ON nv.MaChucVu=cv.MaChucVu
                  JOIN PHONGBAN pb ON nv.MaPhong=pb.MaPhong
                  WHERE nv.MaPhong <> (
                      SELECT MaPhong FROM NHANVIEN WHERE MaNhanVien=?
                  )
                  ORDER BY nv.MaNhanVien
                ";
                $p = [$meId];
            }
            elseif ($myRoleName === 'TruongPhongNhanSuRole') {
                // Xem + chỉnh sửa tất cả nhân viên, trừ lương/phụ cấp của chính mình
                $sql = "
                  SELECT nv.MaNhanVien,nv.HoTen,nv.GioiTinh,
                         DATE_FORMAT(nv.NgaySinh,'%d/%m/%Y') AS NgaySinh,
                         nv.SoDienThoai,
                         CASE WHEN nv.MaNhanVien=? THEN NULL ELSE CAST(AES_DECRYPT(nv.Luong,'nhom6') AS CHAR(20)) END AS Luong,
                         CASE WHEN nv.MaNhanVien=? THEN NULL ELSE CAST(AES_DECRYPT(nv.PhuCap,'nhom6') AS CHAR(20)) END AS PhuCap,
                         nv.MaSoThue,cv.TenChucVu,pb.TenPhong
                  FROM NHANVIEN nv
                  JOIN CHUCVU cv ON nv.MaChucVu=cv.MaChucVu
                  JOIN PHONGBAN pb ON nv.MaPhong=pb.MaPhong
                  ORDER BY nv.MaNhanVien
                ";
                $p = [$meId, $meId];
            }
            

            elseif ($myRole === 5) {
                $sql = "
                    SELECT 
                        nv.MaNhanVien,
                        CASE WHEN nv.MaPhong = ? THEN nv.HoTen ELSE NULL END AS HoTen,
                        CASE WHEN nv.MaPhong = ? THEN nv.GioiTinh ELSE NULL END AS GioiTinh,
                        CASE WHEN nv.MaPhong = ? THEN DATE_FORMAT(nv.NgaySinh,'%d/%m/%Y') ELSE NULL END AS NgaySinh,
                        CASE WHEN nv.MaPhong = ? THEN nv.SoDienThoai ELSE NULL END AS SoDienThoai,
                        CAST(AES_DECRYPT(nv.Luong,'nhom6') AS CHAR(20)) AS Luong,
                        CAST(AES_DECRYPT(nv.PhuCap,'nhom6') AS CHAR(20)) AS PhuCap,
                        nv.MaSoThue,
                        CASE WHEN nv.MaPhong = ? THEN cv.TenChucVu ELSE NULL END AS TenChucVu,
                        CASE WHEN nv.MaPhong = ? THEN pb.TenPhong ELSE NULL END AS TenPhong
                    FROM NHANVIEN nv
                    JOIN CHUCVU cv ON nv.MaChucVu = cv.MaChucVu
                    JOIN PHONGBAN pb ON nv.MaPhong = pb.MaPhong
                    ORDER BY nv.MaNhanVien
                ";
                $p = [$dept, $dept, $dept, $dept, $dept, $dept];
            }

            elseif ($myRole === 6) { // Giám đốc
                $sql = "
                SELECT nv.MaNhanVien,nv.HoTen,nv.GioiTinh,
                        DATE_FORMAT(nv.NgaySinh,'%d/%m/%Y') AS NgaySinh,
                        nv.SoDienThoai,
                        CAST(AES_DECRYPT(nv.Luong,'nhom6') AS CHAR(20)) AS Luong,
                        CAST(AES_DECRYPT(nv.PhuCap,'nhom6') AS CHAR(20)) AS PhuCap,
                        nv.MaSoThue,cv.TenChucVu,pb.TenPhong
                FROM NHANVIEN nv
                JOIN CHUCVU cv ON nv.MaChucVu=cv.MaChucVu
                JOIN PHONGBAN pb ON nv.MaPhong=pb.MaPhong
                ORDER BY nv.MaNhanVien
                ";
                $p = [];
            }

            else {
                $sql = "
                SELECT nv.MaNhanVien,nv.HoTen,nv.GioiTinh,
                        DATE_FORMAT(nv.NgaySinh,'%d/%m/%Y') AS NgaySinh,
                        nv.SoDienThoai,NULL AS Luong,NULL AS PhuCap,
                        nv.MaSoThue,cv.TenChucVu,pb.TenPhong
                FROM NHANVIEN nv
                JOIN CHUCVU cv ON nv.MaChucVu=cv.MaChucVu
                JOIN PHONGBAN pb ON nv.MaPhong=pb.MaPhong
                ORDER BY nv.MaNhanVien
                ";
                $p = [];
            }

            $stmt = $this->db->prepare($sql);
            if ($p) $stmt->bind_param(str_repeat('i', count($p)), ...$p);
            $stmt->execute();
            $res = $stmt->get_result();
            $out = [];
            while ($r = $res->fetch_assoc()) $out[] = $r;

            echo json_encode(['success' => true, 'data' => $out], JSON_UNESCAPED_UNICODE);
        }
        catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
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

        $myId       = (int) $_SESSION['user']['MaNhanVien'];
        $myRole     = (int) $_SESSION['user']['MaChucVu'];
        $myRoleName = $_SESSION['user']['TenRole'];

        // Lấy phòng ban của người dùng
        $stmtDept = $this->db->prepare("SELECT MaPhong FROM NHANVIEN WHERE MaNhanVien = ?");
        $stmtDept->bind_param("i", $myId);
        $stmtDept->execute();
        $myDept = (int)$stmtDept->get_result()->fetch_assoc()['MaPhong'];
        $stmtDept->close();

        $json = file_get_contents('php://input');
        $body = json_decode($json, true);

        // Kiểm tra các trường bắt buộc
        $requiredFields = ['MaNhanVien','HoTen','GioiTinh','NgaySinh','SoDienThoai','Luong','PhuCap','MaSoThue'];
        foreach ($requiredFields as $f) {
            if (!isset($body[$f]) || $body[$f] === '') {
                echo json_encode(['success' => false, 'error' => "Thiếu trường $f"]);
                return;
            }
        }

        $targetId  = (int) $body['MaNhanVien'];
        $hoTen     = $body['HoTen'];
        $gioiTinh  = $body['GioiTinh'];
        $ngaySinh  = $body['NgaySinh'];
        $sdt       = $body['SoDienThoai'];
        $luong     = $body['Luong'];
        $phuCap    = $body['PhuCap'];
        $maSoThue  = $body['MaSoThue'];

        //  Nhân viên thường không được sửa người khác
        if ($myRole === 1 && $myId !== $targetId) {
            http_response_code(403);
            echo json_encode(['success'=>false,'error'=>'Không được chỉnh sửa người khác']);
            return;
        }

        //  Nhân viên phòng nhân sự không được chỉnh sửa nhân viên phòng nhân sự khác
        if ($myRoleName === 'NhanVienNhanSuRole') {
            $stmt = $this->db->prepare("SELECT MaPhong FROM NHANVIEN WHERE MaNhanVien = ?");
            $stmt->bind_param("i", $targetId);
            $stmt->execute();
            $targetDept = (int)$stmt->get_result()->fetch_assoc()['MaPhong'];
            $stmt->close();

            if ($targetDept === $myDept && $targetId !== $myId) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'error' => 'Không được sửa nhân viên phòng nhân sự khác'
                ]);
                return;
            }
        }

        try {
            //  Mặc định: cho phép update đầy đủ (bao gồm Trưởng phòng nhân sự)
            $stmt = $this->db->prepare("
                UPDATE NHANVIEN 
                SET HoTen      = ?,
                    GioiTinh   = ?,
                    NgaySinh   = ?,
                    SoDienThoai= ?,
                    Luong      = AES_ENCRYPT(?, 'nhom6'),
                    PhuCap     = AES_ENCRYPT(?, 'nhom6'),
                    MaSoThue   = ?
                WHERE MaNhanVien = ?
            ");
            $stmt->bind_param(
                "sssssssi",
                $hoTen, $gioiTinh, $ngaySinh,
                $sdt,   $luong,   $phuCap,
                $maSoThue, $targetId
            );

            $stmt->execute();
            echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
        } catch (\Exception $e) {
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
