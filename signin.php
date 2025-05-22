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

          <!-- ==== Custom Combobox Vai trò ==== -->
          <div class="mb-3">
            <label class="form-label">Vai trò</label>
            <div class="custom-select-wrapper">
              <div class="custom-select" id="role-select">
                <div class="custom-select__trigger">
                  <span>Chọn vai trò đăng nhập</span>
                  <div class="arrow"></div>
                </div>
                <div class="custom-options" required>
                  <span class="custom-option" data-value="NhanVienRole">Nhân viên</span>
                  <span class="custom-option" data-value="TruongPhongRole">Trưởng phòng</span>
                  <span class="custom-option" data-value="NhanVienNhanSuRole">NV Phòng nhân sự</span>
                  <span class="custom-option" data-value="TruongPhongNhanSuRole">TP Phòng nhân sự</span>
                  <span class="custom-option" data-value="NhanVienTaiVuRole">NV Phòng tài vụ</span>
                  <span class="custom-option" data-value="GiamDocRole">Giám đốc</span>
                </div>
              </div>
              <!-- hidden input để formData.get('role') vẫn hoạt động -->
              <input type="hidden" name="role" id="role-input" value="">
            </div>
          </div>
          <!-- ========================== -->


          <div class="d-grid">
            <button type="submit" class="btn-login">Đăng nhập</button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<!-- Chỉ import duy nhất loginAPI.js -->
<script src="public/JS/dropdownLogin.js"></script>
<script src="public/JS/loginAPI.js"></script>
<?php require 'template/footer.php'; ?>
