// public/js/employeesFetch.js
document.addEventListener('DOMContentLoaded', async () => {
    const token = localStorage.getItem('token');
  
    try {
      const resp = await fetch('/phpcoban/BMCSDL-Final/api/employees/nhanvien', {
        headers: {
          'Accept': 'application/json',
          ...(token && { 'Authorization': `Bearer ${token}` })
        }
      });
      const result = await resp.json();
  
      if (!resp.ok || !result.success) {
        console.error('API lỗi:', result.error || result.message);
        DraculaModal.show({
          title: 'Lỗi tải danh sách',
          message: result.error || 'Không thể lấy danh sách nhân viên.',
          okText: 'OK'
        });
        return;
      }
  
      const data = result.data;
      // SỬA LẠI QSA SỬ DỤNG CLASS thay vì ID
      const tbody = document.querySelector('.employees-table tbody');
      if (!tbody) {
        console.error('Không tìm thấy <tbody> của .employees-table');
        return;
      }
      tbody.innerHTML = '';
  
      data.forEach((e, i) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${i + 1}</td>
          <td>${e.MaNhanVien}</td>
          <td>${e.HoTen}</td>
          <td>${e.GioiTinh}</td>
          <td>${e.NgaySinh}</td>
          <td>${e.SoDienThoai}</td>
          <td>${e.Luong !== null ? Number(e.Luong).toLocaleString() : '***'}</td>
          <td>${e.PhuCap !== null ? Number(e.PhuCap).toLocaleString() : '***'}</td>
          <td>${e.MaSoThue}</td>
          <td>${e.TenChucVu}</td>
          <td>${e.TenPhong}</td>
        `;
        tbody.appendChild(tr);
      });
  
    } catch (err) {
      console.error('Lỗi kết nối API employees:', err);
      DraculaModal.show({
        title: 'Lỗi kết nối',
        message: 'Không thể kết nối tới server.',
        okText: 'OK'
      });
    }
  });
  