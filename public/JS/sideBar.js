// public/js/sidebar.js
document.addEventListener('DOMContentLoaded', () => {
  const btn     = document.getElementById('sidebar-toggle');
  const sidebar = document.querySelector('.sidebar');
  const main    = document.querySelector('.main-content');

  btn.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    main.classList.toggle('collapsed');
  });
});
