// public/js/sidebar.js

document.addEventListener('DOMContentLoaded', () => {
  const btnToggle = document.getElementById('sidebar-toggle');
  const sidebar   = document.getElementById('app-sidebar');
  // chỉ tìm wrapper nếu page có shiftable
  const wrapper   = document.querySelector('.main-content.shiftable');

  if (!btnToggle || !sidebar) return;

  btnToggle.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    if (wrapper) wrapper.classList.toggle('collapsed');
  });
});
