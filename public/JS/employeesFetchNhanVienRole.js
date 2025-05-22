document.addEventListener('DOMContentLoaded', () => {
  const tbody      = document.querySelector('.employees-table tbody');
  const editModal  = document.getElementById('editModal');
  const closeBtn   = document.getElementById('editModalCloseBtn');
  const form       = document.getElementById('editEmpForm');
  const saveBtn    = form.querySelector('button[type="submit"]');

  if (!tbody || !editModal || !closeBtn || !form || !saveBtn) {
    console.error('Thiếu phần tử trên DOM, vui lòng kiểm tra lại IDs/classes');
    return;
  }

  // --- 1) Fetch danh sách nhân viên và render vào bảng ---
  async function fetchEmployees() {
    tbody.innerHTML = '';
    try {
      const resp = await fetch('/phpcoban/BMCSDL-Final/api/employees/nhanvien', {
        headers:     { 'Accept': 'application/json' },
        credentials: 'include'
      });
      const result = await resp.json();
      if (!resp.ok || !result.success) {
        throw new Error(result.error || result.message);
      }

      result.data.forEach((e, i) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${i + 1}</td>
          <td>${e.MaNhanVien}</td>
          <td>${e.HoTen}</td>
          <td>${e.GioiTinh}</td>
          <td>${e.NgaySinh}</td>
          <td>${e.SoDienThoai}</td>
          <td>${e.Luong  !== null ? Number(e.Luong).toLocaleString() : '**********'}</td>
          <td>${e.PhuCap !== null ? Number(e.PhuCap).toLocaleString() : '**********'}</td>
          <td>${e.MaSoThue}</td>
          <td>${e.TenChucVu}</td>
          <td>${e.TenPhong}</td>
          <td class="action">
            <button type="button" class="action-btn">Sửa</button>
          </td>
        `;
        tbody.appendChild(tr);
      });
    }
    catch (err) {
      console.error('API lỗi:', err);
      DraculaModal.show({
        title:   'Lỗi tải dữ liệu',
        message: err.message,
        okText:  'OK'
      });
    }
  }

  // lần đầu load
  fetchEmployees();

  // lắng nghe để reload khi cần
  document.addEventListener('refreshEmployeesList', fetchEmployees);


  // --- 2) Khi click "Sửa" trên bất cứ row nào ---
  tbody.addEventListener('click', e => {
    const btn = e.target.closest('button.action-btn');
    if (!btn) return;

    // 2.1 Highlight hàng
    tbody.querySelectorAll('tr').forEach(r => r.classList.remove('selected'));
    const row = btn.closest('tr');
    row.classList.add('selected');

    // 2.2 Đổ dữ liệu vào form
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
    // nếu bạn có thêm 2 field:
    if (form.TenChucVu)  form.TenChucVu.value  = cells[9].textContent.trim();
    if (form.TenPhong)   form.TenPhong.value   = cells[10].textContent.trim();

    // 2.3 Mở modal
    editModal.classList.add('open');
  });

  // đóng modal
  closeBtn.addEventListener('click', () => {
    editModal.classList.remove('open');
  });


  // --- 3) Khi submit form --- 
  form.addEventListener('submit', async e => {
    e.preventDefault();
    saveBtn.disabled    = true;
    saveBtn.textContent = 'Đang lưu...';

    const selectedRow = tbody.querySelector('tr.selected');
    if (!selectedRow) {
      DraculaModal.show({ title:'Lỗi', message:'Chưa chọn nhân viên', okText:'OK' });
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
      const resp = await fetch('/phpcoban/BMCSDL-Final/api/employees/update', {
        method:      'PUT',
        headers:     { 'Content-Type':'application/json','Accept':'application/json' },
        credentials: 'include',
        body:        JSON.stringify(payload)
      });
      const result = await resp.json();
      if (!resp.ok || !result.success) {
        throw new Error(result.error || result.message);
      }

      // đóng modal
      editModal.classList.remove('open');

      // reload bảng
      document.dispatchEvent(new Event('refreshEmployeesList'));

      // show popup thành công
      DraculaModal.show({
        title:   'Thành công',
        message: 'Cập nhật nhân viên thành công',
        okText:  'OK'
      });
    }
    catch (err) {
      console.error(err);
      DraculaModal.show({ title:'Lỗi', message:err.message, okText:'OK' });
    }
    finally {
      saveBtn.disabled    = false;
      saveBtn.textContent = 'Lưu thay đổi';
    }
  });
});
