// public/JS/authNavbar.js
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('nav-auth');
  if (!btn) return;

  const token = localStorage.getItem('token');
  if (token) {
    btn.textContent = 'Đăng xuất';
    btn.setAttribute('data-action', 'logout');
  } else {
    btn.textContent = 'Đăng nhập';
    btn.setAttribute('data-action', 'login');
  }

  // Bật event khi nhấn
  btn.addEventListener('click', (e) => {
    e.preventDefault();
    if (token) {
      // ủy quyền logout sẽ do logoutAPI.js xử lý
      return;
    }
    window.location.href = 'signin.php';
  });
});
