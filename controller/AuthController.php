<?php
// controller/AuthController.php
require_once __DIR__ . '/../model/UserModel.php';

class AuthController
{
    protected $db;
    protected $userModel;

    // Token TTL & refresh threshold (giây)
    private $tokenTTL         = 900; // 15 phút
    private $refreshThreshold = 300; // 5 phút

    public function __construct($conn)
    {
        session_start();
        $this->db        = $conn;
        $this->userModel = new UserModel($conn);
    }

    // Hiển thị form đăng nhập
    public function showLogin()
    {
        // Trỏ đến file signin.php ở root
        require __DIR__ . '/../signin.php';
    }

    // Xử lý POST đăng nhập
    public function doLogin()
    {
        $maNV     = trim($_POST['maNV'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($maNV === '' || $password === '') {
            $error = 'Vui lòng nhập đầy đủ thông tin.';
            require __DIR__ . '/../signin.php';
            return;
        }

        $user = $this->userModel->authenticate($maNV, $password);
        if ($user) {
            $_SESSION['user'] = $user;
            $this->issueToken();
            header('Location: index.php?action=profile');
            exit;
        } else {
            $error = 'Sai mã nhân viên hoặc mật khẩu.';
            require __DIR__ . '/../signin.php';
        }
    }

    // Trang thông tin cá nhân (profile)
    public function profile()
    {
        // Nếu chưa login, redirect về login
        if (empty($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        // Kiểm tra token
        if (!$this->validateToken()) {
            $this->logout();
        }
        // Gia hạn token nếu sắp hết
        $this->maybeRefreshToken();

        $user = $_SESSION['user'];
        // Trỏ đến file profile.php ở root
        require __DIR__ . '/../profile.php';
    }

    // Đăng xuất
    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }

    // Sinh token và lưu expiry vào session
    private function issueToken()
    {
        $_SESSION['token']     = bin2hex(random_bytes(16));
        $_SESSION['token_exp'] = time() + $this->tokenTTL;
    }

    // Kiểm tra token còn hiệu lực?
    private function validateToken()
    {
        return !empty($_SESSION['token'])
            && !empty($_SESSION['token_exp'])
            && time() < $_SESSION['token_exp'];
    }

    // Gia hạn token nếu còn ≤ refreshThreshold
    private function maybeRefreshToken()
    {
        $remaining = $_SESSION['token_exp'] - time();
        if ($remaining <= $this->refreshThreshold) {
            $_SESSION['token_exp'] = time() + $this->tokenTTL;
        }
    }
}
