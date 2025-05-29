<?php
// controller/EmployeeController.php
require_once __DIR__ . '/../model/UserModel.php';
require_once __DIR__ . '/../model/AuditLogModel.php';

class EmployeeController
{
    /** @var mysqli */
    protected $db;

    /** @var UserModel */
    protected $userModel;

    protected $auditLogModel;

    public function __construct(mysqli $conn)
    {
        $this->db        = $conn;
        $this->userModel = new UserModel($conn);
        $this->auditLogModel = new AuditLogModel($conn);

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

            // 2) Trưởng phòng (role id = 2): 
            //    – xem **tất cả** nhân viên trong phòng của mình
            //    – được thấy đầy đủ Lương và Phụ cấp
            //    – KHÔNG được phép sửa
            elseif ($myRole === 2) { // Trưởng phòng (trừ nhân sự)
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
                JOIN CHUCVU   cv ON nv.MaChucVu   = cv.MaChucVu
                JOIN PHONGBAN pb ON nv.MaPhong    = pb.MaPhong
                WHERE nv.MaPhong = ?
                ORDER BY nv.MaNhanVien
                ";
                $p = [$dept];
            }

            elseif ($myRoleName === 'NhanVienNhanSuRole') {
                $sql = "
                    SELECT nv.MaNhanVien, nv.HoTen, nv.GioiTinh,
                           DATE_FORMAT(nv.NgaySinh, '%d/%m/%Y') AS NgaySinh,
                           nv.SoDienThoai,
                           CAST(AES_DECRYPT(nv.Luong, 'nhom6') AS CHAR(20)) AS Luong,
                           CAST(AES_DECRYPT(nv.PhuCap, 'nhom6') AS CHAR(20)) AS PhuCap,
                           nv.MaSoThue, cv.TenChucVu, pb.TenPhong
                    FROM NHANVIEN nv
                    JOIN CHUCVU cv ON nv.MaChucVu = cv.MaChucVu
                    JOIN PHONGBAN pb ON nv.MaPhong = pb.MaPhong
                    WHERE pb.TenPhong != 'Phòng nhân sự'
                    ORDER BY nv.MaNhanVien
                ";
                $p = [];
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
            
            // Nhân viên phòng tài vụ: 
            // - Với nv cùng phòng: hiện hết (Họ tên, Giới tính, Ngày sinh, SDT + Lương, Phụ cấp, Mã số thuế, Chức vụ, Phòng).
            // - Với nv phòng khác: chỉ hiện MaNV, Lương, Phụ cấp, Mã số thuế (các trường khác trả NULL để phía JS hiển thị '**********').
            elseif ($myRole === 5) {
                $sql = "
                SELECT 
                    nv.MaNhanVien,
                    CASE WHEN nv.MaPhong = ? THEN nv.HoTen ELSE NULL END         AS HoTen,
                    CASE WHEN nv.MaPhong = ? THEN nv.GioiTinh ELSE NULL END      AS GioiTinh,
                    CASE WHEN nv.MaPhong = ? THEN DATE_FORMAT(nv.NgaySinh,'%d/%m/%Y') ELSE NULL END AS NgaySinh,
                    CASE WHEN nv.MaPhong = ? THEN nv.SoDienThoai ELSE NULL END    AS SoDienThoai,
                    CAST(AES_DECRYPT(nv.Luong,'nhom6') AS CHAR(20))              AS Luong,
                    CAST(AES_DECRYPT(nv.PhuCap,'nhom6') AS CHAR(20))             AS PhuCap,
                    nv.MaSoThue,
                    CASE WHEN nv.MaPhong = ? THEN cv.TenChucVu ELSE NULL END      AS TenChucVu,
                    CASE WHEN nv.MaPhong = ? THEN pb.TenPhong ELSE NULL END       AS TenPhong
                FROM NHANVIEN nv
                JOIN CHUCVU   cv ON nv.MaChucVu = cv.MaChucVu
                JOIN PHONGBAN pb ON nv.MaPhong  = pb.MaPhong
                ORDER BY nv.MaNhanVien
                ";
                // bind 6 lần $dept cho 6 dấu ?
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
            // Ghi audit log VIEW_LIST
            $this->auditLogModel->write(
                'VIEW_LIST',
                'NHANVIEN',
                0,
                null,
                null
            );
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

        // 1) Lấy oldData trước khi cập nhật
        $oldStmt = $this->db->prepare("SELECT * FROM NHANVIEN WHERE MaNhanVien = ?");
        $oldStmt->bind_param("i", $targetId);
        $oldStmt->execute();
        $oldData = $oldStmt->get_result()->fetch_assoc();
        $oldStmt->close();

        //  Nhân viên thường không được sửa người khác
        if ($myRole === 1 && $myId !== $targetId) {
            http_response_code(403);
            echo json_encode(['success'=>false,'error'=>'Không được chỉnh sửa người khác']);
            return;
        }

        // --- Xử lý riêng cho Giám đốc (role id = 6) ---
        if ($myRole === 6) {
            // đọc payload
            $body = json_decode(file_get_contents('php://input'), true);
            $emp = (int) $body['MaNhanVien'];
            $newLuong  = $body['Luong'];
            $newPhuCap = $body['PhuCap'];

            // Chỉ cho phép sửa Lương và Phụ cấp
            $stmt = $this->db->prepare("
                UPDATE NHANVIEN
                SET Luong   = AES_ENCRYPT(?,'nhom6'),
                    PhuCap  = AES_ENCRYPT(?,'nhom6')
                WHERE MaNhanVien = ?
            ");
            $stmt->bind_param("sii", $newLuong, $newPhuCap, $emp);
            if ($stmt->execute()) {
                echo json_encode(['success'=>true,'message'=>'Cập nhật lương & phụ cấp thành công'], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode(['success'=>false,'error'=>'Cập nhật thất bại'], JSON_UNESCAPED_UNICODE);
            }
            return;  // bỏ qua phần xử lý sau
        }

        //  Nhân viên phòng nhân sự không được chỉnh sửa nhân viên phòng nhân sự khác
        // === Chặn HR (NhanVienNhanSuRole) sửa nhân viên Phòng nhân sự ===
        if ($meRoleName === 'NhanVienNhanSuRole') {
            $targetId = $_POST['MaNhanVien'];
            $stmtChk  = $conn->prepare("
                SELECT pb.TenPhong
                FROM NHANVIEN nv
                JOIN PHONGBAN pb ON nv.MaPhong = pb.MaPhong
                WHERE nv.MaNhanVien = ?
            ");
            $stmtChk->execute([$targetId]);
            $row = $stmtChk->fetch(PDO::FETCH_ASSOC);
            if ($row && $row['TenPhong'] === 'Phòng nhân sự') {
                echo json_encode([
                    'status'  => false,
                    'message' => 'Bạn không có quyền sửa nhân viên phòng Nhân sự.'
                ]);
                exit;
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

            // 2) Lấy newData sau khi cập nhật
            $newStmt = $this->db->prepare("SELECT * FROM NHANVIEN WHERE MaNhanVien = ?");
            $newStmt->bind_param("i", $targetId);
            $newStmt->execute();
            $newData = $newStmt->get_result()->fetch_assoc();
            $newStmt->close();

            // Lấy ID mới và gộp newData
            $newId = (int)$this->db->insert_id;
            $newData = [
                'MaNhanVien'    => $newId,
                'HoTen'         => $body['HoTen'],
                'GioiTinh'      => $body['GioiTinh'],
                'NgaySinh'      => $body['NgaySinh'],
                'SoDienThoai'   => $body['SoDienThoai'],
                'Luong'         => $body['Luong'],
                'PhuCap'        => $body['PhuCap'],
                'MaSoThue'      => $body['MaSoThue'],
                // ... thêm tất cả vừa tạo
            ];
            // Ghi audit log CREATE
            $this->auditLogModel->write(
                'CREATE',
                'NHANVIEN',
                $newId,
                null,
                $newData
            );
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
            echo json_encode(['success' => false, 'error' => 'Chưa đăng nhập'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $myRole = (int) $_SESSION['user']['MaChucVu'];
        if ($myRole === 1) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Không có quyền thực hiện hành động này'], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Đọc input JSON
        $json = file_get_contents('php://input');
        $body = json_decode($json, true);

        // Validate dữ liệu đầu vào
        $required = ['HoTen','GioiTinh','NgaySinh','SoDienThoai','Luong','PhuCap','MaSoThue','MaChucVu','MaPhong'];
        foreach ($required as $f) {
            if (!isset($body[$f]) || $body[$f] === '') {
                echo json_encode(['success' => false, 'error' => "Thiếu trường $f"], JSON_UNESCAPED_UNICODE);
                return;
            }
        }

        // Lấy từng biến về
        $hoTen     = $body['HoTen'];
        $gioiTinh  = $body['GioiTinh'];
        $ngaySinh  = $body['NgaySinh'];
        $sdt       = $body['SoDienThoai'];
        $luong     = $body['Luong'];
        $phuCap    = $body['PhuCap'];
        $maSoThue  = $body['MaSoThue'];
        $maChucVu  = (int)$body['MaChucVu'];
        $maPhong   = (int)$body['MaPhong'];

        try {
            // 1) Thực hiện INSERT
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
            $stmt->bind_param(
                'sssssssii',
                $hoTen, $gioiTinh, $ngaySinh, $sdt,
                $luong, $phuCap,
                $maSoThue, $maChucVu, $maPhong
            );
            $stmt->execute();

            // 2) Lấy ID mới và chuẩn bị dữ liệu để log
            $newId = (int)$this->db->insert_id;
            $newData = [
                'MaNhanVien'  => $newId,
                'HoTen'       => $hoTen,
                'GioiTinh'    => $gioiTinh,
                'NgaySinh'    => $ngaySinh,
                'SoDienThoai' => $sdt,
                'Luong'       => $luong,
                'PhuCap'      => $phuCap,
                'MaSoThue'    => $maSoThue,
                'MaChucVu'    => $maChucVu,
                'MaPhong'     => $maPhong,
            ];

            // 3) Ghi audit-log với action CREATE_EMPLOYEE
            $this->writeAuditLog(
                'CREATE_EMPLOYEE', // phải khớp với key trong JS mapping
                'NHANVIEN',
                $newId,
                null,       // old_data
                $newData    // new_data
            );

            // 4) Trả về JSON thành công
            echo json_encode([
                'success' => true,
                'message' => 'Tạo nhân viên thành công',
                'newId'   => $newId
            ], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            http_response_code(500);
            // Nếu có lỗi PHP, ensure chỉ trả JSON chứ không echo thêm gì để JS parse được.
            echo json_encode([
                'success' => false,
                'error'   => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Ghi audit log.
     * Gọi sau mỗi thao tác thành công của Trưởng phòng nhân sự hoặc Giám đốc.
     */
    protected function writeAuditLog(
        string $action,
        string $table,
        int    $recordId,
        ?array $oldData,
        ?array $newData
    ) {
        $user = $_SESSION['user'];
        $ip   = $_SERVER['REMOTE_ADDR'] ?? null;

        $sql = "
        INSERT INTO audit_log 
            (action, table_name, record_id, old_data, new_data, user_id, user_name, user_role, ip_address)
        VALUES (?,?,?,?,?,?,?,?,?)
        ";
        $stmt = $this->db->prepare($sql);

        // JSON_UNESCAPED_UNICODE để giữ ký tự tiếng Việt
        $jsonOld = $oldData ? json_encode($oldData, JSON_UNESCAPED_UNICODE) : null;
        $jsonNew = $newData ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null;

        $stmt->bind_param(
        'ssississs',
        $action,
        $table,
        $recordId,
        $jsonOld,
        $jsonNew,
        $user['MaNhanVien'],
        $user['HoTen'],
        $user['TenRole'],
        $ip
        );
        $stmt->execute();
    }

    /**
     * API trả về toàn bộ bản ghi audit_log
     */
    public function apiGetAuditLogs()
    {
        header('Content-Type: application/json; charset=utf-8');
        session_start();  // nếu controller chưa gọi

        // 1) bảo đảm đã login
        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['success'=>false,'error'=>'Chưa đăng nhập'], JSON_UNESCAPED_UNICODE);
            return;
        }

        // 2) chỉ Trưởng phòng nhân sự và Giám đốc được xem
        $role = $_SESSION['user']['TenRole'] ?? '';
        if (! in_array($role, ['TruongPhongNhanSuRole','GiamDocRole'])) {
            http_response_code(403);
            echo json_encode(['success'=>false,'error'=>'Không có quyền xem audit log'], JSON_UNESCAPED_UNICODE);
            return;
        }

        // 3) đọc từ audit_log
        try {
            $stmt = $this->db->prepare("SELECT * FROM audit_log ORDER BY created_at DESC");
            $stmt->execute();
            $res = $stmt->get_result();
            $logs = [];
            while ($r = $res->fetch_assoc()) {
                $logs[] = $r;
            }
            echo json_encode(['success'=>true,'data'=>$logs], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success'=>false,'error'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

}
