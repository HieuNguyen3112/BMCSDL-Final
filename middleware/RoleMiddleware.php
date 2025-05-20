<?php
// middleware/RoleMiddleware.php

class RoleMiddleware
{
    protected $db;

    public function __construct(mysqli $conn)
    {
        $this->db = $conn;
        session_start();
    }

    /**
     * @param  string|array  $roles  Một role hoặc mảng role được phép
     */
    public function handle($roles)
    {
        // 1) ép phải đã login
        if (empty($_SESSION['user'])) {
            header('Location: /phpcoban/BMCSDL-Final/signin.php');
            exit;
        }

        // 2) Lấy role hiện tại của user từ session hoặc load lại từ DB
        //    Giả sử $_SESSION['user']['MaChucVu'] lưu id chức vụ
        $maChucVu = $_SESSION['user']['MaChucVu'];

        // 3) Tra cứu tên role (TenRole) từ bảng CHUCVU
        $stmt = $this->db->prepare("
            SELECT TenRole 
            FROM CHUCVU 
            WHERE MaChucVu = ?
            LIMIT 1
        ");
        $stmt->bind_param('i', $maChucVu);
        $stmt->execute();
        $stmt->bind_result($tenRole);
        $stmt->fetch();
        $stmt->close();

        if (! $tenRole) {
            http_response_code(403);
            echo "Không tìm thấy quyền của bạn.";
            exit;
        }

        // 4) Kiểm tra xem có trong $roles không
        $allowed = is_array($roles)
            ? in_array($tenRole, $roles, true)
            : $tenRole === $roles;

        if (! $allowed) {
            http_response_code(403);
            // nếu API
            if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(['error' => 'Bạn không có quyền truy cập tính năng này.']);
            } else {
                echo "<h1>403 - Bạn không có quyền truy cập.</h1>";
            }
            exit;
        }

        // nếu thành công, cho phép chạy tiếp
    }
}
