<?php
// sidebar.php
?>
<aside id="app-sidebar" class="sidebar">
  <button id="sidebar-toggle" class="sidebar-toggle">
    <i class="fa fa-chevron-left"></i>
  </button>
  <ul class="sidebar-menu">
    <li class="sidebar-item<?php if (basename($_SERVER['PHP_SELF']) === 'profile.php') echo ' active'; ?>">
      <a href="profile.php">
        <i class="fa fa-user"></i>
        <span class="sidebar-text">Thông tin cá nhân</span>
      </a>
    </li>
    <li class="sidebar-item<?php if (basename($_SERVER['PHP_SELF']) === 'employees.php') echo ' active'; ?>">
      <a href="employees.php">
        <i class="fa fa-book"></i>
        <span class="sidebar-text">Danh sách nhân viên</span>
      </a>
    </li>
    <li class="sidebar-item">
      <a href="#" id="logout-btn" data-action="logout">
        <i class="fa fa-sign-out-alt"></i>
        <span class="sidebar-text">Đăng xuất</span>
      </a>  
    </li>
  </ul>
</aside>
<script src="public/JS/modal.js"></script>
