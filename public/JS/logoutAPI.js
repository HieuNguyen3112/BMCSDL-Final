// public/JS/logout.js
document.addEventListener('DOMContentLoaded', () => {
    // Chọn nút logout (sẽ gắn id="logout-btn" vào <a> trong sidebar)
    const logoutBtn = document.getElementById('logout-btn');
    if (!logoutBtn) return;
  
    logoutBtn.addEventListener('click', async (e) => {
      e.preventDefault();
  
      try {
        // Gọi API logout
        await fetch('/phpcoban/BMCSDL-Final/logout', {
          method: 'GET',
          credentials: 'include'
        });
      } catch (err) {
        console.error('Lỗi khi logout:', err);
      } finally {
        // Chuyển về trang đăng nhập
        window.location.href = 'signin.php';
      }
    });
  });
  