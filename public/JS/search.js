document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.site-navbar .search-container');
    const input = form.querySelector('.search-input');
    const icon  = form.querySelector('.search-icon');
  
    // 1) Click icon lần đầu → mở rộng, focus vào input
    icon.addEventListener('click', e => {
      if (!form.classList.contains('active')) {
        e.preventDefault();           
        form.classList.add('active');
        setTimeout(() => input.focus(), 300);
      }
    });
  
    // 2) Click ra ngoài → đóng lại
    document.addEventListener('click', e => {
      if (!form.contains(e.target) && form.classList.contains('active')) {
        form.classList.remove('active');
      }
    });
  
    // 3) Nhấn Esc khi đang mở → đóng lại
    input.addEventListener('keydown', e => {
      if (e.key === 'Escape') {
        form.classList.remove('active');
      }
    });
  });
  