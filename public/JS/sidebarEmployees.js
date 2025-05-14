// public/js/sideBarEmployees.js

function initSidebar() {
  const btnToggle = document.getElementById('sidebar-toggle');
  const sidebar = document.getElementById('app-sidebar');
  const content = document.querySelector('.main-content');

  if (!btnToggle || !sidebar || !content) return;

  // Đảm bảo sự kiện chỉ được bind một lần
  btnToggle.removeEventListener('click', toggleSidebar);
  btnToggle.addEventListener('click', toggleSidebar);

  function toggleSidebar() {
    sidebar.classList.toggle('collapsed');
    content.classList.toggle('collapsed');
  }
}

// Đảm bảo hàm được gọi ngay khi trang load
document.addEventListener('DOMContentLoaded', initSidebar);
