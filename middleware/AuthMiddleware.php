<?php
// middleware/AuthMiddleware.php
session_start();

class AuthMiddleware
{
    /**
     * Bắt buộc đã login (nếu chưa thì redirect về signin.php)
     */
    public static function checkLogin()
    {
        if (empty($_SESSION['user'])) {
            header('Location: /phpcoban/BMCSDL-Final/signin.php');
            exit;
        }
    }

    /**
     * Bắt buộc user phải nằm trong 1 trong số các role cho phép
     * @param array $allowedRoles mảng tên role, ví dụ ['NhanVienRole','TruongPhongRole']
     */
    public static function checkRole(array $allowedRoles)
    {
        self::checkLogin();
        $role = $_SESSION['user']['TenRole'] ?? null;
        if (!in_array($role, $allowedRoles, true)) {
            http_response_code(403);
            echo '<h2 style="text-align:center;color:#ff5555;">403 Forbidden</h2>';
            echo '<p style="text-align:center;">Bạn không có quyền truy cập trang này.</p>';
            exit;
        }
    }
}
