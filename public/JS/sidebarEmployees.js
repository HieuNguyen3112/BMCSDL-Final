// Đảm bảo đoạn script này chỉ chạy trên trang employees.php
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('app-sidebar');
    const mainContent = document.querySelector('.main-content');

    btn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');
    });
});
