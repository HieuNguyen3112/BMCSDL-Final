// public/js/sidebarEmployees.js
document.addEventListener('DOMContentLoaded', () => {
  const sidebar   = document.getElementById('app-sidebar');
  const toggleBtn = document.getElementById('sidebar-toggle');
  const mainWrap  = document.querySelector('.main-content');

  if (sidebar && toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      // 1) Toggle collapsed trên sidebar
      const isCollapsed = sidebar.classList.toggle('collapsed');

      // 2) CHỈ LÙI nội dung nếu mainWrap có class "shiftable" (tức là employees.php)
      if (mainWrap && mainWrap.classList.contains('shiftable')) {
        mainWrap.classList.toggle('collapsed', isCollapsed);
      }

      // 3) Đổi chiều icon mũi tên
      const icon = toggleBtn.querySelector('i');
      if (icon) {
        icon.classList.toggle('fa-chevron-left', !isCollapsed);
        icon.classList.toggle('fa-chevron-right', isCollapsed);
      }
    });
  }
});
