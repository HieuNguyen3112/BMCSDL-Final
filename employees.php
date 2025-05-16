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
          <tr>
            <td>1</td>
            <td>100000</td>
            <td>Nguyễn Văn Toàn</td>
            <td>Nam</td>
            <td>5/8/2024</td>
            <td>0123456789</td>
            <td>19,000,000</td>
            <td>2,400,000</td>
            <td>1225255529</td>
            <td>Nhân viên</td>
            <td>Phòng IT</td>
          </tr>
          <tr>
            <td>2</td>
            <td>100001</td>
            <td>Trần Đình Trọng</td>
            <td>Nam</td>
            <td>3/27/1998</td>
            <td>0987654321</td>
            <td>21,000,000</td>
            <td>2,500,000</td>
            <td>8563489385</td>
            <td>Trưởng phòng</td>
            <td>Phòng Kinh doanh</td>
          </tr>
          <tr>
            <td>3</td>
            <td>100002</td>
            <td>Đoàn Văn Hậu</td>
            <td>Nam</td>
            <td>3/12/1999</td>
            <td>0912345678</td>
            <td>18,500,000</td>
            <td>2,200,000</td>
            <td>9638284638</td>
            <td>Nhân viên</td>
            <td>Phòng Hành chính</td>
          </tr>
          <tr>
            <td>4</td>
            <td>100010</td>
            <td>Hồ Xuân Nga</td>
            <td>Nữ</td>
            <td>2/1/1990</td>
            <td>0901234567</td>
            <td>20,000,000</td>
            <td>2,300,000</td>
            <td>3483748774</td>
            <td>Nhân viên</td>
            <td>Phòng IT</td>
          </tr>
          <!-- ... thêm dòng khác nếu cần ... -->
        </tbody>
      </table>
    </div>
  </div>
  <script src="public/js/sidebarEmployees.js"></script>
</div>

<?php
require 'template/footer.php';
?>


