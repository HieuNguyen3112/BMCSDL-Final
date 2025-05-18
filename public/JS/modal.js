// public/JS/modal.js
// Bản sửa đổi: loại bỏ listener tự động redirect và expose DraculaModal để sử dụng từ loginAPI.js
//               tự động phát hiện type error nếu không truyền type explicit

document.addEventListener('DOMContentLoaded', () => {
  const overlay  = document.getElementById('app-modal');
  const closeBtn = document.getElementById('modal-close');
  const okBtn    = document.getElementById('modal-ok');
  const icon     = document.getElementById('modal-icon');
  const titleEl  = document.getElementById('modal-title');
  const msgEl    = document.getElementById('modal-message');

  function showModal({ title = 'Thông báo', message = '', type }) {
    // Nếu không có type, tự động phát hiện lỗi theo keyword trong title
    let modalType = type;
    if (!modalType) {
      const tLow = title.toLowerCase();
      if (tLow.includes('thất bại') || tLow.includes('lỗi')) {
        modalType = 'error';
      } else {
        modalType = 'success';
      }
    }

    // Reset class và style
    icon.className = 'modal-icon';
    icon.style.color = '';
    
    // Set icon và màu theo type
    switch (modalType) {
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

  // Expose DraculaModal ra global để dùng trong loginAPI.js
  window.DraculaModal = {
    show: showModal,
    hide: hideModal
  };
});
