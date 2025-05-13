// sidebar.js
document.addEventListener('DOMContentLoaded', function() {
  const btn = document.getElementById('sidebar-toggle');
  const sidebar = document.getElementById('app-sidebar');
  
  btn.addEventListener('click', function() {
    sidebar.classList.toggle('collapsed');
  });
});
