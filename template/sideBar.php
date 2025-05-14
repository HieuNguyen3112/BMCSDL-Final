<?php
// sidebar.php
?>
<aside id="app-sidebar" class="sidebar">
  <div id="sidebar-toggle" class="sidebar-toggle">
    <i class="fa fa-chevron-left"></i>
  </div>
  <ul class="sidebar-menu">
    <li class="sidebar-item <?= ($active==='profile')?'active':'' ?>">
      <a href="profile.php">
        <i class="fa fa-user"></i>
        <span class="sidebar-text">Thông tin cá nhân</span>
      </a>
    </li>
    <li class="sidebar-item <?= ($active==='employees')?'active':'' ?>">
      <a href="employees.php">
        <i class="fa fa-list"></i>
        <span class="sidebar-text">Danh sách nhân viên</span>
      </a>
    </li>
    <li class="sidebar-item">
      <a href="index.php?controller=logout">
        <i class="fa fa-sign-out-alt"></i>
        <span class="sidebar-text">Đăng xuất</span>
      </a>
    </li>
  </ul>
</aside>

