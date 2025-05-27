<?php
session_start();
require 'template/header.php';
require __DIR__ . '/template/sidebar.php';
?>

<!-- Styles -->
<link rel="stylesheet" href="public/CSS/sidebar.css">
<link rel="stylesheet" href="public/CSS/employees.css">
<link rel="stylesheet" href="public/CSS/auditLog.css">

<div class="main-content shiftable">
  <div class="auditlog-container">
    <h2>Nhật ký hoạt động (Audit Log)</h2>
    <div class="table-wrapper">
      <table class="employees-table">
        <thead>
          <tr>
            <th>STT</th>
            <th>Thời gian</th>
            <th>Người thao tác</th>
            <th>Hành động</th>
            <th>Chi tiết</th>
          </tr>
        </thead>
        <tbody id="auditlog-body">
          <!-- Dữ liệu sẽ được đổ vào đây bởi auditLog.js -->
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Khai báo mảng role cho phép, trước khi load roleCheck.js -->
<script>
window.ALLOWED_ROLES = [
    'GiamDocRole',
    'NhanVienNhanSuRole',
    'TruongPhongNhanSuRole'
  ];
</script>

<!-- Scripts -->
<script src="public/JS/roleCheck.js"></script>
<script src="public/js/auditLogSidebar.js"></script>
<script src="public/js/sidebar.js"></script>
<script src="public/JS/auditLogFetchAPI.js"></script>
<script src="public/JS/logoutAPI.js"></script>

<?php require 'template/footer.php'; ?>
