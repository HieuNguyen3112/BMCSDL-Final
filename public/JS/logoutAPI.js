// public/JS/logoutAPI.js
document.addEventListener('DOMContentLoaded', () => {
  document.body.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-action="logout"], #logout-btn');
    if (!btn) return;

    e.preventDefault();
    const token = localStorage.getItem('token') || '';
    // Gọi API để destroy session
    try {
      await fetch('./logout', {
        method: 'GET',
        credentials: 'include'
      });
    } catch (err) {
      console.error('Logout API error:', err);
      DraculaModal.show({
        title: 'Lỗi đăng xuất',
        message: 'Không thể kết nối máy chủ.'
      });
      return;
    }

    // Xóa token cục bộ
    localStorage.removeItem('token');

    // Thông báo thành công
    DraculaModal.show({
      title: 'Đăng xuất thành công',
      message: 'Bạn đã đăng xuất khỏi hệ thống.'
    });

    // Redirect về signin, thay thế history
    setTimeout(() => {
      window.location.replace('signin.php');
    }, 1500);
  });
});
