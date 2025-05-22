// public/JS/logoutAPI.js
document.addEventListener('DOMContentLoaded', () => {
  document.body.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-action="logout"], #logout-btn');
    if (!btn) return;

    e.preventDefault();

    try {
      // Gọi đúng tới route /api/logout
      const resp = await fetch('api/logout', {
        method: 'GET',
        credentials: 'include'
      });

      // Nếu server trả về lỗi HTTP
      if (!resp.ok) {
        throw new Error(`HTTP ${resp.status}`);
      }

      const contentType = resp.headers.get('Content-Type') || '';
      // Xóa token cục bộ trước để chắc chắn
      localStorage.removeItem('token');

      // Nếu là API trả JSON (có Bearer token)
      if (contentType.includes('application/json')) {
        const body = await resp.json();
        DraculaModal.show({
          title: 'Đăng xuất thành công',
          message: body.message || 'Bạn đã đăng xuất khỏi hệ thống.'
        });
        setTimeout(() => {
          // redirect theo server trả về
          window.location.replace(body.redirect || 'signin.php');
        }, 1200);
      } else {
        // Trường hợp header('Location: …')—fetch sẽ follow và trả HTML
        DraculaModal.show({
          title: 'Đăng xuất thành công',
          message: 'Bạn đã đăng xuất khỏi hệ thống.'
        });
        setTimeout(() => {
          window.location.replace('signin.php');
        }, 1200);
      }

    } catch (err) {
      console.error('Lỗi khi gọi logout API:', err);
      DraculaModal.show({
        title: 'Lỗi đăng xuất',
        message: 'Không thể kết nối tới server.'
      });
    }
  });
});
