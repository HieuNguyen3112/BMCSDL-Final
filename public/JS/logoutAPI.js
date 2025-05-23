// public/JS/logoutAPI.js
document.addEventListener('DOMContentLoaded', () => {
  async function performLogout() {
    try {
      const token   = localStorage.getItem('token');
      const options = { method: 'GET', credentials: 'include' };
      if (token) options.headers = { 'Authorization': `Bearer ${token}` };

      const resp = await fetch('/phpcoban/BMCSDL-Final/api/logout', options);

      // Xóa token sau khi gọi API
      localStorage.removeItem('token');
      localStorage.removeItem('refreshToken');

      const contentType = resp.headers.get('Content-Type') || '';
      if (contentType.includes('application/json')) {
        const body = await resp.json();
        await DraculaModal.show({
          title:   'Đăng xuất thành công',
          message: body.message  || 'Bạn đã đăng xuất khỏi hệ thống.',
          okText:  'OK'
        });
        setTimeout(() => {
          window.location.replace(body.redirect || 'signin.php');
        }, 1200);
      } else {
        await DraculaModal.show({
          title:   'Đăng xuất thành công',
          message: 'Bạn đã đăng xuất khỏi hệ thống.',
          okText:  'OK'
        });
        setTimeout(() => {
          window.location.replace('signin.php');
        }, 1200);
      }
    } catch (err) {
      console.error('Lỗi khi gọi logout API:', err);
      await DraculaModal.show({
        title:   'Lỗi đăng xuất',
        message: 'Không thể kết nối tới server.',
        okText:  'OK'
      });
    }
  }

  // CHỈ lắng nghe event custom doLogout
  document.addEventListener('doLogout', performLogout);
});
