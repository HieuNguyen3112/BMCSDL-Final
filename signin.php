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

        <?php if(!empty($_SESSION['error'])): ?>
          <div class="alert alert-danger py-1">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
          </div>
        <?php endif; ?>

        <form id="login-form" action="./controller/AuthController.php" method="post">
          <input type="hidden" name="action" value="login">
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

<?php require 'template/footer.php'; ?>
<script>
  // khi DOM sẵn sàng
  document.addEventListener('DOMContentLoaded', () => {
    const frm = document.getElementById('login-form');

    frm.addEventListener('submit', e => {
      e.preventDefault();     // chặn gửi form lên server
      // hiển thị modal thành công
      DraculaModal.show({
        title: 'Đăng nhập thành công',
        message: 'Chào mừng bạn đã đăng nhập vào hệ thống!'
      });
      // sau 2s tự chuyển hướng sang profile.php
      setTimeout(() => {
        window.location.href = 'profile.php';
      }, 2000);
    });
  });
</script>
