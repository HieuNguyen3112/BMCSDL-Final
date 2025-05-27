// public/js/editAPI.js
document.addEventListener('DOMContentLoaded', () => {
    const tbody        = document.querySelector('.employees-table tbody');
    const editModal    = document.getElementById('editModal');
    const closeBtn     = document.getElementById('editModalCloseBtn');
    const form         = document.getElementById('editEmpForm');
    const saveBtn      = form.querySelector('button[type="submit"]');
  
    if (!tbody || !editModal || !closeBtn || !form || !saveBtn) {
      console.error('Thiếu phần tử DOM cho chức năng Edit');
      return;
    }
  
    let currentUserRole = '';
    // --- Lấy lại role (để chặn NhanVienRole) ---
    fetch('/phpcoban/BMCSDL-Final/api/profile', {
      headers:     { 'Accept': 'application/json' },
      credentials: 'include'
    })
      .then(r => r.json())
      .then(body => {
        if (body.success) currentUserRole = body.data.TenRole;
      })
      .catch(err => console.error(err));
  
    // --- Bắt sự kiện click “Sửa” ---
    tbody.addEventListener('click', e => {
      const btn = e.target.closest('button.action-btn');
      if (!btn || btn.textContent.trim() !== 'Sửa') return;
      if (currentUserRole === 'NhanVienRole') return;  // chặn với nhân viên thường
  
      // highlight dòng
      tbody.querySelectorAll('tr').forEach(r => r.classList.remove('selected'));
      const row = btn.closest('tr');
      row.classList.add('selected');
  
      // đổ dữ liệu vào form
      const cells = row.children;
      form.HoTen.value       = cells[2].textContent.trim();
      form.GioiTinh.value    = cells[3].textContent.trim();
      {
        const [d,m,y] = cells[4].textContent.trim().split('/');
        form.NgaySinh.value = `${y}-${m.padStart(2,'0')}-${d.padStart(2,'0')}`;
      }
      form.SoDienThoai.value = cells[5].textContent.trim();
      form.Luong.value       = cells[6].textContent.trim().replace(/\./g,'');
      form.PhuCap.value      = cells[7].textContent.trim().replace(/\./g,'');
      form.MaSoThue.value    = cells[8].textContent.trim();
      if (form.TenChucVu)    form.TenChucVu.value  = cells[9].textContent.trim();
      if (form.TenPhong)     form.TenPhong.value   = cells[10].textContent.trim();
  
      // mở modal edit
      editModal.classList.add('open');
    });
  
    // --- Đóng modal ---
    closeBtn.addEventListener('click', () => {
      editModal.classList.remove('open');
    });
  
    // --- Xử lý submit form edit ---
    form.addEventListener('submit', async e => {
      e.preventDefault();
      saveBtn.disabled    = true;
      saveBtn.textContent = 'Đang lưu...';
  
      const selectedRow = tbody.querySelector('tr.selected');
      if (!selectedRow) {
        await DraculaModal.show({
          title:   'Lỗi',
          message: 'Chưa chọn nhân viên để sửa.',
          okText:  'OK'
        });
        saveBtn.disabled    = false;
        saveBtn.textContent = 'Lưu thay đổi';
        return;
      }
  
      const MaNhanVien = selectedRow.cells[1].textContent.trim();
      const payload = {
        MaNhanVien,
        HoTen:       form.HoTen.value.trim(),
        GioiTinh:    form.GioiTinh.value,
        NgaySinh:    form.NgaySinh.value,
        SoDienThoai: form.SoDienThoai.value.trim(),
        Luong:       form.Luong.value,
        PhuCap:      form.PhuCap.value,
        MaSoThue:    form.MaSoThue.value.trim()
      };
  
      try {
        const token   = localStorage.getItem('token');
        const headers = { 'Content-Type':'application/json', 'Accept':'application/json' };
        if (token) headers['Authorization'] = `Bearer ${token}`;
  
        const resp = await fetch('/phpcoban/BMCSDL-Final/api/employees/update', {
          method:      'PUT',
          headers,
          credentials: 'include',
          body:        JSON.stringify(payload)
        });
        const result = await resp.json();
        if (!resp.ok || !result.success) {
          throw new Error(result.error || result.message || 'Lỗi khi cập nhật.');
        }
  
        // Đóng modal, báo thành công & reload list
        editModal.classList.remove('open');
        await DraculaModal.show({
          title:   'Thành công',
          message: 'Cập nhật nhân viên thành công!',
          okText:  'OK'
        });
        document.dispatchEvent(new Event('refreshEmployeesList'));
      } catch (err) {
        console.error(err);
        editModal.classList.remove('open');
        await DraculaModal.show({
          title:   'Lỗi',
          message: err.message || 'Đã có lỗi xảy ra khi cập nhật.',
          okText:  'OK'
        });
        editModal.classList.add('open');
      } finally {
        saveBtn.disabled    = false;
        saveBtn.textContent = 'Lưu thay đổi';
      }
    });
  });
  