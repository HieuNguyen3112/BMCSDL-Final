<?php
session_start();
require 'template/header.php';
require __DIR__ . '/template/sidebar.php';
?>
<div class="main-content shiftable">
  <link rel="stylesheet" href="public/css/createEmployee.css">
  <link rel="stylesheet" href="public/css/sidebar.css">

  <div class="form-container">
    <h2>Tạo mới nhân viên</h2>
    <form id="createEmpForm">
      <div class="grid-2cols">
        <div class="form-group">
          <label for="hoTen">Họ tên</label>
          <input
            type="text"
            id="hoTen"
            name="HoTen"
            placeholder="Nhập họ tên nhân viên"
            required
          >
        </div>
        <div class="form-group">
          <label for="gioiTinh">Giới tính</label>
          <select id="gioiTinh" name="GioiTinh" required>
            <option value="" disabled selected>Chọn giới tính</option>
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
          </select>
        </div>
        <div class="form-group">
          <label for="ngaySinh">Ngày sinh</label>
          <input
            type="date"
            id="ngaySinh"
            name="NgaySinh"
            placeholder="dd/mm/yyyy"
            required
          >
        </div>
        <div class="form-group">
          <label for="soDienThoai">Số điện thoại</label>
          <input
            type="text"
            id="soDienThoai"
            name="SoDienThoai"
            placeholder="Nhập số điện thoại"
            required
          >
        </div>
        <div class="form-group">
          <label for="luong">Lương</label>
          <input
            type="number"
            id="luong"
            name="Luong"
            placeholder="Ví dụ: 15000000"
            required
          >
        </div>
        <div class="form-group">
          <label for="phuCap">Phụ cấp</label>
          <input
            type="number"
            id="phuCap"
            name="PhuCap"
            placeholder="Ví dụ: 2000000"
            required
          >
        </div>
        <div class="form-group">
          <label for="maSoThue">Mã số thuế</label>
          <input
            type="text"
            id="maSoThue"
            name="MaSoThue"
            placeholder="Nhập mã số thuế"
            required
          >
        </div>
        <div class="form-group">
          <label for="tenChucVu">Chức vụ</label>
          <input
            type="text"
            id="tenChucVu"
            name="TenChucVu"
            placeholder="Nhập tên chức vụ"
            required
          >
        </div>
        <div class="form-group">
          <label for="tenPhong">Tên phòng</label>
          <input
            type="text"
            id="tenPhong"
            name="TenPhong"
            placeholder="Nhập tên phòng ban"
            required
          >
        </div>
        <input type="hidden" name="MaChucVu" value="<?= $_SESSION['user']['MaChucVu'] ?>">
        <input type="hidden" name="MaPhong"  value="<?= $_SESSION['user']['MaPhong'] ?>">
      </div>
      <button type="submit" class="btn-submit">Tạo nhân viên</button>
    </form>
  </div>

  <script src="public/js/sidebar.js"></script>
  <script src="public/js/createEmployeeSidebar.js"></script>
  <<script src="public/js/employeesCreateAPI.js"></script>
</div>
<?php
require 'template/footer.php';
?>
