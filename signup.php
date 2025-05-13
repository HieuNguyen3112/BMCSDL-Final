<?php 
session_start();
require 'template/header.php'; 
?>

<div class="card p-4 shadow-lg" style="max-width: 500px; width:100%; background-color: #282a36; border-radius: 12px; color: #f8f8f2;">
  <h3 class="text-center mb-4" style="color: #8be9fd;">Đăng Ký Tài Khoản</h3>

  <?php if(!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger py-1">
      <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
  <?php elseif(!empty($_SESSION['success'])): ?>
    <div class="alert alert-success py-1">
      <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
  <?php endif; ?>

  <form action="./controller/AuthController.php" method="post">
    <input type="hidden" name="action" value="signup">

    <div class="row g-2">
      <div class="col-md">
        <label class="form-label" style="color: #bd93f9;">Họ</label>
        <input 
          type="text" 
          name="firstname" 
          class="form-control" 
          style="background-color: #44475a; border: none; color: #f8f8f2;" 
          placeholder="Nhập họ của bạn" 
          required
        >
      </div>
      <div class="col-md">
        <label class="form-label" style="color: #bd93f9;">Tên</label>
        <input 
          type="text" 
          name="lastname" 
          class="form-control" 
          style="background-color: #44475a; border: none; color: #f8f8f2;" 
          placeholder="Nhập tên của bạn" 
          required
        >
      </div>
    </div>

    <div class="mb-3 mt-3">
      <label class="form-label" style="color: #bd93f9;">Tên đăng nhập</label>
      <input 
        type="text" 
        name="username" 
        class="form-control" 
        style="background-color: #44475a; border: none; color: #f8f8f2;" 
        placeholder="Nhập username" 
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
        placeholder="Nhập mật khẩu" 
        required
      >
    </div>

    <button type="submit" class="btn-login w-100">Đăng ký</button>
  </form>

  <div class="text-center mt-3">
    <a href="signin.php" class="bottom-link">Đã có tài khoản? Đăng nhập</a>
  </div>
</div>

<?php require 'template/footer.php'; ?>
