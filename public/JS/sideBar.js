document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('sidebar-toggle');
  const sb  = document.getElementById('app-sidebar');
  const mc  = document.querySelector('.main-content');
  btn.addEventListener('click', () => {
    sb.classList.toggle('collapsed');
    mc.classList.toggle('collapsed');
  });
});
