document.addEventListener('DOMContentLoaded', () => {
  const select = document.getElementById('role-select');
  const trigger = select.querySelector('.custom-select__trigger');
  const options = select.querySelectorAll('.custom-option');
  const hiddenInput = document.getElementById('role-input');

  // Mở / đóng dropdown
  trigger.addEventListener('click', () => {
    select.classList.toggle('open');
  });

  // Chọn 1 option
  options.forEach(option => {
    option.addEventListener('click', () => {
      const value = option.getAttribute('data-value');
      const label = option.textContent;
      // Cập nhật label hiển thị
      trigger.querySelector('span').textContent = label;
      // Cập nhật hidden input để formData.get('role') có giá trị
      hiddenInput.value = value;
      // đóng dropdown
      select.classList.remove('open');
    });
  });

  // Đóng khi click ngoài
  document.addEventListener('click', e => {
    if (!select.contains(e.target)) {
      select.classList.remove('open');
    }
  });
});
