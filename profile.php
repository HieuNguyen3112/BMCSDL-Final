<?php session_start(); require 'template/header.php'; ?>
<?php require __DIR__ . '/template/sidebar.php'; ?>
<div class="main-content">
<link rel="stylesheet" href="public/css/sidebar.css">
<link rel="stylesheet" href="public/css/profile.css">

  <div class="profile-container">
    <section class="profile-form">
      <h2>Thông tin nhân viên</h2>
      <form id="emp-form" action="controller/EmployeeController.php" method="post">
        <!-- SLIDER WRAPPER -->
        <div class="form-slider">
          <!-- STEP 1 -->
          <div class="form-step form-step-active">
            <div class="grid-2cols">
              <div class="form-group">
                <label for="emp_id">Mã nhân viên</label>
                <input type="text" id="emp_id" name="emp_id" value="100000" readonly>
              </div>
              <div class="form-group">
                <label for="emp_name">Họ tên</label>
                <input type="text" id="emp_name" name="emp_name" value="Nguyễn Văn Toàn">
              </div>
              <div class="form-group">
                <label>Giới tính</label>
                <input type="text" name="gender" value="Nam">
              </div>
              <div class="form-group">
                <label for="dob">Ngày sinh</label>
                <input type="text" id="dob" name="dob" value="5/8/2024">
              </div>
              <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="text" id="phone" name="phone" value="0123456789">
              </div>
            </div>
          </div>

          <!-- STEP 2 -->
          <div class="form-step">
            <div class="grid-2cols">
              <div class="form-group">
                <label for="salary">Lương</label>
                <input type="text" id="salary" name="salary" value="19,000,000">
              </div>
              <div class="form-group">
                <label for="allowance">Phụ cấp</label>
                <input type="text" id="allowance" name="allowance" value="2,400,000">
              </div>
              <div class="form-group">
                <label for="tax_id">Mã số thuế</label>
                <input type="text" id="tax_id" name="tax_id" value="1225255529">
              </div>
              <div class="form-group">
                <label for="position">Tên chức vụ</label>
                <input type="text" id="position" name="position" value="Nhân viên">
              </div>
              <div class="form-group">
                <label for="department">Tên phòng</label>
                <input type="text" id="department" name="department" value="Phòng IT">
              </div>
            </div>
          </div>
        </div>
        <!-- /form-slider -->

        <!-- Navigation -->
        <div class="form-navigation">
          <button type="button" class="step-btn btn-prev">
            <i class="fa fa-arrow-left"></i>
          </button>
          <button type="button" class="step-btn btn-next">
            <i class="fa fa-arrow-right"></i>
          </button>
          <button type="submit" class="btn-save">Lưu thay đổi</button>
        </div>
      </form>
    </section>
  </div>
  <script src="public/js/profile.js"></script>
</div>


<script src="public/js/sidebar.js"></script>


<?php
require 'template/footer.php';
?>

