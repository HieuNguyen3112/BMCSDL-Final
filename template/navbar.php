<?php
// template/navbar.php
?>

<link rel="stylesheet" href="public/css/navbar.css">

<nav class="site-navbar">
  <div class="nav-left">
    <a href="index.php" class="navbar-brand">
      <span class="gradient-text">Nhóm 6</span> Quản lý nhân sự
    </a>
  </div>
  <div class="nav-center">
  <form action="search.php" method="get" class="search-form">
    <input type="text" name="q" placeholder="Search..." />
    <button type="submit" aria-label="Search">
      <i class="fa fa-search"></i>
    </button>
  </form>
  </div>
  <div class="nav-right">
    <a href="index.php" class="nav-link">Home</a>
    <a href="tasks.php" class="nav-link">Làm việc</a>
    <button class="btn-login" onclick="location.href='signin.php'">Đăng nhập</button>
  </div>
</nav>
