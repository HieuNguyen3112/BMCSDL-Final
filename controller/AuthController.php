<?php 
session_start();
require 'template/header.php'; 
?>

<div class="text-center mb-4">
    <span class="heading-gradient">Nhóm 6</span>
    <span class="heading-text"> - Quản lý nhân sự</span>
</div>

<div class="card p-4 shadow-lg" style="max-width: 400px; width:100%; background-color: #282a36; border-radius: 12px; color: #f8f8f2;">
  <h3 class="text-center mb-4" style="color: #8be9fd;">Đăng Nhập</h3>

  <?php if(!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger py-1">
      <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
  <?php endif; ?>

  <form action="./controller/AuthController.php" method="post">
    <input type="hidden" name="action" value="login">

    <div class="mb-3">
      <label class="form-label" style="color: #bd93f9;">Mã nhân viên</label>
      <input 
        type="text" 
        name="username" 
        class="form-control" 
        style="background-color: #44475a; border: none; color: #f8f8f2;" 
        placeholder="Mã nhân viên của bạn" 
        required
      >
    </div>

    <div class="mb-3">
      <label class="form-label" style="color: #bd93f9;">Mật khẩu</label>
      <input 
        type="password" 
        name="password" 
        class="form-control" 
        style="background-color: #44475a; border: none; color: #f8f8f2;" 
        placeholder="Mật khẩu của bạn" 
        required
      >
    </div>

    <div class="d-grid">
      <button type="submit" class="btn" style="background-color: #6272a4; color: #f8f8f2;">Đăng nhập</button>
    </div>
  </form>
</div>

<?php require 'template/footer.php'; ?>
