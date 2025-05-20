// public/JS/authNavbar.js
document.addEventListener('DOMContentLoaded', () => {
  // nút Đăng nhập/Đăng xuất (button)
  const btnAuth = document.getElementById('nav-auth');

  // link Home và Làm việc (anchors)
  const btnHome = document.querySelector('.nav-link[href="index.php"]');
  const btnWork = document.querySelector('.nav-link[href="tasks.php"]');

  const token    = localStorage.getItem('token');
  const username = localStorage.getItem('username'); // giả định bạn lưu username khi login

  // --- Thiết lập nút Auth ---
  if (btnAuth) {
    if (token) {
      btnAuth.textContent = 'Đăng xuất';
      btnAuth.setAttribute('data-action', 'logout');
    } else {
      btnAuth.textContent = 'Đăng nhập';
      btnAuth.setAttribute('data-action', 'login');
    }
    btnAuth.addEventListener('click', e => {
      e.preventDefault();
      if (token) {
        // phát sự kiện để logoutAPI.js bắt và xử lý
        document.dispatchEvent(new Event('doLogout'));
      } else {
        window.location.href = 'signin.php';
      }
    });
  }

  // --- Home ---
  if (btnHome) {
    btnHome.addEventListener('click', e => {
      e.preventDefault();
      if (token) {
        window.location.href = 'workspace.php';
      } else {
        DraculaModal.show({
          title: 'Chưa đăng nhập',
          message: 'Vui lòng đăng nhập để truy cập Home.',
          type: 'info'
        });
      }
    });
  }

  // --- Làm việc ---
  if (btnWork) {
    btnWork.addEventListener('click', e => {
      e.preventDefault();
      if (token) {
        window.location.href = 'profile.php';
      } else {
        DraculaModal.show({
          title: 'Chưa đăng nhập',
          message: 'Vui lòng đăng nhập để vào không gian làm việc.',
          type: 'info'
        });
      }
    });
  }
});
