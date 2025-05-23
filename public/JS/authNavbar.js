// public/JS/authNavbar.js
document.addEventListener('DOMContentLoaded', () => {
  const btnAuth          = document.getElementById('nav-auth');
  const btnSidebarLogout = document.getElementById('logout-btn');

  function handleAuthClick(e) {
    e.preventDefault();
    e.stopPropagation();            // NGĂN event click chồng chéo
    const token = localStorage.getItem('token');
    if (token) {
      // chỉ phát event custom, không làm gì khác
      document.dispatchEvent(new Event('doLogout'));
    } else {
      window.location.href = 'signin.php';
    }
  }

  // Navbar button
  if (btnAuth) {
    const token = localStorage.getItem('token');
    if (token) {
      btnAuth.textContent        = 'Đăng xuất';
      btnAuth.setAttribute('data-action', 'logout');
    } else {
      btnAuth.textContent        = 'Đăng nhập';
      btnAuth.setAttribute('data-action', 'login');
    }
    btnAuth.addEventListener('click', handleAuthClick);
  }

  // Sidebar “Đăng xuất”
  if (btnSidebarLogout) {
    btnSidebarLogout.addEventListener('click', handleAuthClick);
  }

  // --- Home ---
  const btnHome = document.querySelector('.nav-link[href="index.php"]');
  if (btnHome) {
    btnHome.addEventListener('click', e => {
      e.preventDefault();
      if (localStorage.getItem('token')) {
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
  const btnWork = document.querySelector('.nav-link[href="tasks.php"]');
  if (btnWork) {
    btnWork.addEventListener('click', e => {
      e.preventDefault();
      if (localStorage.getItem('token')) {
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
