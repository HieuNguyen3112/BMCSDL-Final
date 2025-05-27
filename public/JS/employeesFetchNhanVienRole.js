// public/js/employeesFetchNhanVienRole.js
document.addEventListener('DOMContentLoaded', () => {
  const tbody = document.querySelector('.employees-table tbody');
  if (!tbody) {
    console.error('Không tìm thấy <tbody> của bảng nhân viên');
    return;
  }

  let currentUserRole = '';
  // --- Lấy role user hiện tại ---
  fetch('/phpcoban/BMCSDL-Final/api/profile', {
    headers:     { 'Accept': 'application/json' },
    credentials: 'include'
  })
    .then(r => r.json())
    .then(body => {
      if (body.success) {
        currentUserRole = body.data.TenRole;
        console.log('Vai trò hiện tại:', currentUserRole);
      }
    })
    .catch(err => console.error('Lỗi khi gọi API profile:', err));

  // --- Hàm fetch & render danh sách ---
  async function fetchEmployees() {
    tbody.innerHTML = '';
    try {
      const resp = await fetch('/phpcoban/BMCSDL-Final/api/employees/nhanvien', {
        headers:     { 'Accept': 'application/json' },
        credentials: 'include'
      });
      const result = await resp.json();
      if (!resp.ok || !result.success) {
        throw new Error(result.error || result.message || 'Lỗi khi lấy danh sách');
      }

      result.data.forEach((e, i) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${i + 1}</td>
          <td>${e.MaNhanVien}</td>
          <td>${e.HoTen       ?? '**********'}</td>
          <td>${e.GioiTinh    ?? '**********'}</td>
          <td>${e.NgaySinh    ?? '**********'}</td>
          <td>${e.SoDienThoai ?? '**********'}</td>
          <td>${
            e.Luong !== null && !isNaN(Number(e.Luong))
              ? Number(e.Luong).toLocaleString()
              : '**********'
          }</td>
          <td>${
            e.PhuCap !== null && !isNaN(Number(e.PhuCap))
              ? Number(e.PhuCap).toLocaleString()
              : '**********'
          }</td>
          <td>${e.MaSoThue}</td>
          <td>${e.TenChucVu  ?? '**********'}</td>
          <td>${e.TenPhong   ?? '**********'}</td>
          <td class="action">
            <button type="button" class="action-btn">Sửa</button>
          </td>
        `;
        tbody.appendChild(tr);
      });
    } catch (err) {
      console.error('API lỗi:', err);
      await DraculaModal.show({
        title:   'Lỗi tải dữ liệu',
        message: err.message,
        okText:  'OK'
      });
    }
  }

  // lần đầu & khi có sự kiện refresh
  fetchEmployees();
  document.addEventListener('refreshEmployeesList', fetchEmployees);
});
