<?php 
session_start();
require 'template/header.php'; 
?>

<link rel="stylesheet" href="public/css/signin.css">

<div class="main-content">
  <div class="login-wrapper">
    <div class="login-left">
      <h1><span class="gradient-text">Nhóm 6</span> Quản lý nhân sự</h1>
      <p class="subtitle">Quản lý nhân sự cho doanh nghiệp.</p>
    </div>
    <div class="login-right">
      <div class="card">
        <h3 class="text-center">Đăng Nhập</h3>

        <?php if (!empty($_SESSION['error'])): ?>
          <div class="alert alert-danger py-1">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
          </div>
        <?php endif; ?>

        <form id="login-form">
          <div class="mb-3">
            <label class="form-label">Mã nhân viên</label>
            <input type="text" name="username" class="form-control" placeholder="Mã nhân viên của bạn" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" class="form-control" placeholder="Mật khẩu của bạn" required>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn-login">Đăng nhập</button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<!-- Chỉ import duy nhất loginAPI.js -->
<script src="public/JS/loginAPI.js"></script>
<?php require 'template/footer.php'; ?>
