<?php
session_start();
require 'template/header.php';
?>

<?php require __DIR__ . '/template/sidebar.php'; ?>
<div class="main-content shiftable">
  <link rel="stylesheet" href="public/css/sidebar.css">
  <link rel="stylesheet" href="public/css/employees.css">
  <div class="employees-container">
    <h2>Danh sách thông tin nhân viên</h2>
    <div class="table-wrapper">
      <table class="employees-table">
        <thead>
          <tr>
            <th>STT</th>
            <th>Mã nhân viên</th>
            <th>Họ tên</th>
            <th>Giới tính</th>
            <th>Ngày sinh</th>
            <th>SDT</th>
            <th>Lương</th>
            <th>Phụ cấp</th>
            <th>Mã số thuế</th>
            <th>Tên chức vụ</th>
            <th>Tên phòng</th>
          </tr>
        </thead>
        <tbody>
          <!-- Dữ liệu sẽ được JS đổ vào tại đây -->
        </tbody>
      </table>
    </div>
  </div>
  <script src="public/js/sidebarEmployees.js"></script>
  <script src="public/js/employeesFetchNhanVienRole.js"></script>
</div>

<?php
require 'template/footer.php';
?>


