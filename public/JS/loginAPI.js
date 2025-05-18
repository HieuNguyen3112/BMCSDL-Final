// public/JS/loginAPI.js
document.addEventListener('DOMContentLoaded', () => {
    const frm = document.getElementById('login-form');
  
    frm.addEventListener('submit', async (e) => {
      e.preventDefault();
  
      // Lấy dữ liệu từ form
      const formData = new FormData(frm);
      const payload = {
        maNV: formData.get('username'),
        password: formData.get('password'),
      };
  
      try {
        const response = await fetch('/phpcoban/BMCSDL-Final/api/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload),
        });
  
        // Luôn parse JSON bất kể status
        const result = await response.json();
  
        if (!response.ok) {
          // 4xx/5xx từ API → báo lỗi và dừng
          DraculaModal.show({
            title: 'Đăng nhập thất bại',
            message: result.error || 'Sai mã nhân viên hoặc mật khẩu',
          });
          return;
        }
  
        // response.ok thì mới vào đây
        if (result.success) {
          DraculaModal.show({
            title: 'Đăng nhập thành công',
            message: 'Chào mừng bạn đã đăng nhập vào hệ thống!',
          });
          // Nếu cần token cho các request sau:
          localStorage.setItem('token', result.token);
  
          setTimeout(() => {
            window.location.href = 'profile.php';
          }, 1500);
        } else {
          // Trường hợp API trả 200 nhưng success=false
          DraculaModal.show({
            title: 'Đăng nhập thất bại',
            message: result.error || result.message || 'Sai mã nhân viên hoặc mật khẩu',
          });
        }
      } catch (err) {
        DraculaModal.show({
          title: 'Lỗi kết nối',
          message: 'Không thể kết nối tới server!',
        });
      }
    });
  });
  