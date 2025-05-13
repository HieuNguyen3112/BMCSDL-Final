// public/js/modal.js
document.addEventListener('DOMContentLoaded', () => {
  const overlay = document.getElementById('app-modal');
  const closeBtn = document.getElementById('modal-close');
  const okBtn    = document.getElementById('modal-ok');
  const icon     = document.getElementById('modal-icon');
  const titleEl  = document.getElementById('modal-title');
  const msgEl    = document.getElementById('modal-message');

  function showModal({ title = 'Thông báo', message = '', type = 'success' }) {
    icon.className = 'modal-icon';
    switch (type) {
      case 'error':
        icon.firstElementChild.className = 'fa fa-times-circle';
        icon.style.color = '#ff5555';
        break;
      case 'info':
        icon.firstElementChild.className = 'fa fa-info-circle';
        icon.style.color = '#8be9fd';
        break;
      default:
        icon.firstElementChild.className = 'fa fa-check-circle';
        icon.style.color = '#50fa7b';
    }
    titleEl.textContent = title;
    msgEl.textContent   = message;
    overlay.classList.remove('hidden');
  }
  function hideModal() {
    overlay.classList.add('hidden');
  }

  closeBtn.addEventListener('click', hideModal);
  okBtn.addEventListener('click', hideModal);

  // Bắt sự kiện form Đăng nhập
  const loginForm = document.querySelector('.login-wrapper form');
  if (loginForm) {
    loginForm.addEventListener('submit', e => {
      e.preventDefault();
      showModal({
        title: 'Đăng nhập thành công',
        message: 'Chào mừng bạn đã đăng nhập vào hệ thống!',
        type: 'success'
      });
      // sau khi người dùng nhấn OK → chuyển trang với hiệu ứng
      okBtn.addEventListener('click', () => {
        fadeOutAndRedirect('profile.php');
      }, { once: true });
    });
  }
});
