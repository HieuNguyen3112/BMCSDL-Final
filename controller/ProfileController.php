<?php
// controller/ProfileController.php
require_once __DIR__ . '/../model/UserModel.php';

class ProfileController
{
    protected $userModel;

    public function __construct(mysqli $conn)
    {
        $this->userModel = new UserModel($conn);
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    // Hiển thị trang profile
    public function show()
    {
        if (empty($_SESSION['user'])) {
            header('Location: /phpcoban/BMCSDL-Final/signin.php');
            exit;
        }

        $maNV = (int) $_SESSION['user']['MaNhanVien'];
        $user = $this->userModel->getById($maNV);
        require __DIR__ . '/../view/profile.php';
    }

    // API: trả về JSON thông tin user hiện tại
    public function apiShow()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $maNV = (int) $_SESSION['user']['MaNhanVien'];
        $userInfo = $this->userModel->getById($maNV);
        if (! $userInfo) {
            http_response_code(404);
            echo json_encode(['success'=>false,'error'=>'User not found'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // **CHỖ NÀY**: thêm role từ session vào kết quả trả về
        $userInfo['TenRole'] = $_SESSION['user']['TenRole'];

        echo json_encode([
            'success' => true,
            'data'    => $userInfo
        ], JSON_UNESCAPED_UNICODE);
    }

    // —————————————————————————————————————————————
    // 2) API: Cập nhật profile hiện tại
    // PUT /api/profile
    public function apiUpdate()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'error'   => 'Phương thức không hợp lệ'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error'   => 'Chưa đăng nhập'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        // đọc JSON body
        $input = json_decode(file_get_contents('php://input'), true);
        $maNV  = (int) $_SESSION['user']['MaNhanVien'];

        // Chỉ cho phép sửa 4 trường cơ bản
        $data = [
            'HoTen'       => trim($input['HoTen']       ?? ''),
            'GioiTinh'    => trim($input['GioiTinh']    ?? ''),
            'NgaySinh'    => trim($input['NgaySinh']    ?? ''),
            'SoDienThoai' => trim($input['SoDienThoai'] ?? '')
        ];

        // Bạn có thể thêm validate ở đây...

        $ok = $this->userModel->updatePersonal($maNV, $data);
        if (! $ok) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error'   => 'Cập nhật thất bại'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Nếu thành công, trả luôn bản ghi mới
        $updated = $this->userModel->getById($maNV);
        echo json_encode([
            'success' => true,
            'data'    => $updated
        ], JSON_UNESCAPED_UNICODE);
    }
}
