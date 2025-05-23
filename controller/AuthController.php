<?php
// controller/AuthController.php
require_once __DIR__ . '/../model/UserModel.php';
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

class AuthController
{
    protected $db;
    protected $userModel;

    private $tokenTTL         = 3600;  // Thời gian hiệu lực token (giây) = 15 phút
    private $refreshTTL       = 604800;  // Ngưỡng làm mới token (giây) = 7 phút

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
        header('Content-Type: application/json; charset=utf-8');
        // đọc body
        $input = json_decode(file_get_contents('php://input'), true);
        $maNV     = trim($input['maNV']     ?? '');
        $password = trim($input['password'] ?? '');
        $role     = trim($input['role']     ?? '');

        // 1) validate bắt buộc
        if ($maNV === '' || $password === '' || $role === '') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error'   => 'Vui lòng nhập đầy đủ mã NV, mật khẩu và chọn vai trò.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        // 2) xác thực credentials
        $user = $this->userModel->authenticate($maNV, $password);
        if (! $user) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error'   => 'Mã NV hoặc mật khẩu không đúng.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        // 3) so khớp vai trò
        //   giả sử UserModel::authenticate trả về ['MaNhanVien', 'HoTen', ..., 'TenRole']
        if ($user['TenRole'] !== $role) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error'   => 'Vai trò không khớp.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        // 4) nếu hợp lệ, issue token và trả về
        $_SESSION['user']  = $user;
        $this->issueToken();  // phương thức của bạn vẫn dùng
        $this->issueRefreshToken();
        echo json_encode([
            'success' => true,
            'token'   => $_SESSION['token'],
            'refreshToken' => $_SESSION['refresh_token'],
            'expires' => $_SESSION['token_exp'],
            'data'    => $user
        ], JSON_UNESCAPED_UNICODE);
    }

    // Mới: API trả access token mới
    public function apiRefresh()
    {
        header('Content-Type: application/json; charset=utf-8');
        $input = json_decode(file_get_contents('php://input'), true);
        $sentRT = trim($input['refreshToken'] ?? '');

        if (
          empty($_SESSION['refresh_token'])
          || $sentRT !== $_SESSION['refresh_token']
          || time() > ($_SESSION['refresh_token_exp'] ?? 0)
        ) {
          http_response_code(401);
          echo json_encode([
            'success' => false,
            'message' => 'Refresh token không hợp lệ hoặc đã hết hạn.'
          ]);
          return;
        }

        // Issue mới access + refresh
        $this->issueToken();
        $this->issueRefreshToken();
        echo json_encode([
          'success'      => true,
          'token'        => $_SESSION['token'],
          'refreshToken' => $_SESSION['refresh_token'],
          'expires'      => $_SESSION['token_exp']
        ]);
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
        // 1) Khởi động session nếu chưa
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // 2) Lấy header Authorization (nếu client gửi token Bearer)
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] 
                    ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] 
                    ?? '';

        // 4) Xóa session
        $_SESSION = [];
        session_unset();
        session_destroy();

        // 5) Nếu có Bearer token hoặc gọi API thì trả JSON kèm redirect
        if (preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode([
                'message'  => 'Đăng xuất thành công',
                'redirect' => '/phpcoban/BMCSDL-Final/signin.php'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        header('Location: /phpcoban/BMCSDL-Final/signin.php');
        exit;
    }

    // Tạo token ngẫu nhiên và lưu vào session
    private function issueToken()
    {
        $_SESSION['token']     = bin2hex(random_bytes(16));
        $_SESSION['token_exp'] = time() + $this->tokenTTL;
    }

    // Mới: sinh refresh token
    private function issueRefreshToken()
    {
        $_SESSION['refresh_token']     = bin2hex(random_bytes(32));
        $_SESSION['refresh_token_exp'] = time() + $this->refreshTTL;
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
