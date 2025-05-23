// File: public/js/dropdownLogin.js

document.addEventListener('DOMContentLoaded', () => {
  const select       = document.getElementById('role-select');
  if (!select) return;

  const trigger      = select.querySelector('.custom-select__trigger');
  const optionsPanel = select.querySelector('.custom-options');
  const options      = optionsPanel.querySelectorAll('.custom-option');
  const hiddenInput  = document.getElementById('role-input');

  // Mở / đóng dropdown
  trigger.addEventListener('click', () => {
    select.classList.toggle('open');
  });

  // Chọn 1 option
  options.forEach(option => {
    option.addEventListener('click', () => {
      const value = option.getAttribute('data-value');
      const label = option.textContent;
      trigger.querySelector('span').textContent = label;
      hiddenInput.value = value;
      select.classList.remove('open');
    });
  });

  // CHẶN việc click scrollbar (mousedown) làm đóng dropdown
  optionsPanel.addEventListener('mousedown', e => {
    e.stopPropagation();
  });

  // Đóng khi click ngoài
  document.addEventListener('click', e => {
    if (!select.contains(e.target)) {
      select.classList.remove('open');
    }
  });
});
