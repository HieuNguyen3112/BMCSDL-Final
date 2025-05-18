<?php
// controller/AuthController.php
require_once __DIR__ . '/../model/UserModel.php';

class AuthController
{
    protected $db;
    protected $userModel;

    private $tokenTTL         = 900;  // Thời gian hiệu lực token (giây) = 15 phút
    private $refreshThreshold = 300;  // Ngưỡng làm mới token (giây) = 5 phút

    public function __construct($conn)
    {
        $this->db        = $conn;
        $this->userModel = new UserModel($conn);
    }

    // Hiển thị form đăng nhập web
    public function showLogin()
    {
        // Giả sử có file view signin.php
        require __DIR__ . '/../signin.php';
    }

    // Xử lý đăng nhập từ form web
    public function doLogin()
    {
        $maNV     = trim($_POST['maNV'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($maNV === '' || $password === '') {
            $error = 'Vui lòng nhập đầy đủ thông tin.';
            require __DIR__ . '/../signin.php';
            return;
        }

        // Gọi model xác thực
        $user = $this->userModel->authenticate($maNV, $password);
        if ($user) {
            // Đăng nhập thành công: lưu thông tin user vào SESSION và tạo token
            $_SESSION['user'] = $user;
            $this->issueToken();
            header('Location: /profile');
            exit;
        } else {
            $error = 'Sai mã nhân viên hoặc mật khẩu.';
            require __DIR__ . '/../signin.php';
        }
    }

    // Xử lý đăng nhập API (nhận JSON)
    public function apiLogin()
    {
        // Lấy dữ liệu JSON từ request body
        $input = json_decode(file_get_contents('php://input'), true);
        $maNV     = trim($input['maNV'] ?? '');
        $password = trim($input['password'] ?? '');

        // Kiểm tra dữ liệu đầu vào
        if ($maNV === '' || $password === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Vui lòng nhập đầy đủ thông tin.']);
            return;
        }

        // Gọi model để xác thực
        $user = $this->userModel->authenticate($maNV, $password);
        if ($user) {
            // Đăng nhập thành công, lưu thông tin và tạo token
            $_SESSION['user'] = $user;
            $this->issueToken();
            // Trả về kết quả JSON gồm token và thời hạn
            echo json_encode([
                'success' => true,
                'token'   => $_SESSION['token'],
                'expires' => $_SESSION['token_exp']
            ]);
        } else {
            // Đăng nhập thất bại
            http_response_code(401);
            echo json_encode(['error' => 'Sai mã nhân viên hoặc mật khẩu.']);
        }
    }

    // Trang thông tin cá nhân (chỉ cho phép nếu đã đăng nhập)
    public function profile()
    {
        if (empty($_SESSION['user']) || !$this->validateToken()) {
            header('Location: /signin');
            exit;
        }
        // Gia hạn token nếu sắp hết hạn
        $this->maybeRefreshToken();
        // Hiển thị trang profile (giả sử có file view)
        require __DIR__ . '/../profile.php';
    }

    // Đăng xuất: xóa session và chuyển về trang đăng nhập
    public function logout()
    {
        session_destroy();
        header('Location: /signin');
        exit;
    }

    // Tạo token ngẫu nhiên và lưu vào session
    private function issueToken()
    {
        $_SESSION['token']     = bin2hex(random_bytes(16));
        $_SESSION['token_exp'] = time() + $this->tokenTTL;
    }

    // Kiểm tra token còn hiệu lực
    private function validateToken(): bool
    {
        return
            !empty($_SESSION['token']) &&
            !empty($_SESSION['token_exp']) &&
            time() < $_SESSION['token_exp'];
    }

    // Gia hạn token nếu sắp hết hạn
    private function maybeRefreshToken()
    {
        $left = $_SESSION['token_exp'] - time();
        if ($left <= $this->refreshThreshold) {
            $_SESSION['token_exp'] = time() + $this->tokenTTL;
        }
    }
}
