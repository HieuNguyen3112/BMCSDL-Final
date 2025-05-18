// public/JS/authNavbar.js
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('nav-auth');
    if (!btn) return;
  
    const token = localStorage.getItem('token');
  
    if (token) {
      // Đã login → chuyển thành nút Đăng xuất
      btn.textContent = 'Đăng xuất';
    } else {
      // Chưa login → nút Đăng nhập
      btn.textContent = 'Đăng nhập';
    }
  
    // Bật/tắt sự kiện
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      if (token) {
        // Khi logout, để logoutAPI.js xử lý sự kiện click trên #logout-btn
        // Chúng ta chỉ cần để cho logoutAPI.js có listener sẵn
        // (nó đã `document.getElementById('logout-btn')` ở DOMContentLoaded)
      } else {
        // Chuyển đến trang login
        window.location.href = 'signin.php';
      }
    });
  });
  