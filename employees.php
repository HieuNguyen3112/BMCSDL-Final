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
            <!-- THÊM CỘT THAO TÁC -->
            <th class="action">Thao tác</th>
          </tr>
        </thead>
        <tbody>

        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal hiển thị thông tin -->
    <div id="infoModal" class="modal-overlay">
    <div class="modal-content">
      <button id="modalCloseBtn" class="close-btn">&times;</button>
      <h3>Thông tin nhân viên</h3>
      <div class="modal-body">
        <!-- Nội dung được JS inject vào đây -->
      </div>
    </div>
  </div>

  <!-- Modal chỉnh sửa nhân viên -->
  <div id="editModal" class="modal-overlay">
    <div class="modal-content">
      <button type="button" id="editModalCloseBtn" class="close-btn">&times;</button>
      <h3>Chỉnh sửa nhân viên</h3>
      <form id="editEmpForm">
        <div class="grid-2cols">
          <div class="form-group">
            <label for="editHoTen">Họ tên</label>
            <input type="text" id="editHoTen" name="HoTen" required>
          </div>
          <div class="form-group">
            <label for="editGioiTinh">Giới tính</label>
            <select id="editGioiTinh" name="GioiTinh" required>
              <option value="Nam">Nam</option>
              <option value="Nữ">Nữ</option>
            </select>
          </div>
          <div class="form-group">
            <label for="editNgaySinh">Ngày sinh</label>
            <input type="date" id="editNgaySinh" name="NgaySinh" required>
          </div>
          <div class="form-group">
            <label for="editSDT">Số điện thoại</label>
            <input type="text" id="editSDT" name="SoDienThoai" required>
          </div>
          <div class="form-group">
            <label for="editLuong">Lương</label>
            <input type="number" id="editLuong" name="Luong" required>
          </div>
          <div class="form-group">
            <label for="editPhuCap">Phụ cấp</label>
            <input type="number" id="editPhuCap" name="PhuCap" required>
          </div>
          <div class="form-group">
            <label for="editMST">Mã số thuế</label>
            <input type="text" id="editMST" name="MaSoThue" required>
          </div>
          <div class="form-group">
            <label for="editChucVu">Chức vụ</label>
            <input type="text" id="editChucVu" name="TenChucVu" required>
          </div>
          <div class="form-group">
            <label for="editPhong">Tên phòng</label>
            <input type="text" id="editPhong" name="TenPhong" required>
          </div>
        </div>
        <button type="submit" class="btn-save">Lưu thay đổi</button>
      </form>
    </div>
  </div>

  <script src="public/js/sidebarEmployees.js"></script>
  <script src="public/js/fetchWithRefresh.js"></script>
  <script src="public/js/employeesFetchNhanVienRole.js"></script>
  <script src="public/js/editAPI.js"></script>
  <script src="public/js/editModal.js"></script>
  <script src="public/js/roleControl.js"></script>
</div>

<?php
require 'template/footer.php';
?>
