document.addEventListener('DOMContentLoaded', () => {
  // 1) Chọn đúng form
  const form  = document.querySelector('nav.site-navbar .search-form');
  if (!form) return;   // nếu ko tìm thấy, abort luôn

  // 2) Các thành phần con
  const input = form.querySelector('input[name="q"]');
  const btn   = form.querySelector('button[type="submit"]');

  // 3) Click vào button/icon để mở rộng
  btn.addEventListener('click', e => {
    e.preventDefault();
    form.classList.toggle('active');
    if (form.classList.contains('active')) {
      // sau khi mở xong, focus vào input
      setTimeout(() => input.focus(), 300);
    } else {
      // nếu thu gọn thì clear luôn giá trị (nếu muốn)
      // input.value = '';
    }
  });

  // 4) Click ra ngoài để đóng lại
  document.addEventListener('click', e => {
    if (!form.contains(e.target) && form.classList.contains('active')) {
      form.classList.remove('active');
    }
  });

  // 5) Nhấn Esc cũng đóng
  input.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      form.classList.remove('active');
    }
  });
});
