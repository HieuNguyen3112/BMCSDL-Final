document.addEventListener('DOMContentLoaded', () => {
  const form    = document.getElementById('createEmpForm');
  const saveBtn = form.querySelector('button[type="submit"]');

  form.addEventListener('submit', async e => {
    e.preventDefault();
    saveBtn.disabled    = true;
    saveBtn.textContent = 'Đang tạo...';

    // 1) Xử lý ngày
    let rawDate = form.NgaySinh.value.trim();
    let isoDate = rawDate;
    if (rawDate.includes('/')) {
      const parts = rawDate.split('/');
      // parts = [dd, MM, yyyy]
      isoDate = `${parts[2]}-${parts[1].padStart(2,'0')}-${parts[0].padStart(2,'0')}`;
    }

    // 2) Build payload
    const payload = {
      HoTen:       form.HoTen.value.trim(),
      GioiTinh:    form.GioiTinh.value,
      NgaySinh:    isoDate,
      SoDienThoai: form.SoDienThoai.value.trim(),
      Luong:       form.Luong.value.trim().replace(/\./g,''),
      PhuCap:      form.PhuCap.value.trim().replace(/\./g,''),
      MaSoThue:    form.MaSoThue.value.trim(),
      MaChucVu:    parseInt(form.MaChucVu.value, 10),
      MaPhong:     parseInt(form.MaPhong.value, 10)
    };

    try {
      const resp = await fetch('/phpcoban/BMCSDL-Final/api/employees/create', {
        method:      'POST',
        headers:     {
          'Accept':       'application/json',
          'Content-Type': 'application/json'
        },
        credentials: 'include',
        body:         JSON.stringify(payload)
      });

      const result = await resp.json();
      if (!resp.ok || result.success === false) {
        throw new Error(result.error || result.message || 'Lỗi tạo nhân viên');
      }

      // thông báo thành công
      DraculaModal.show({
        title:   'Thành công',
        message: 'Tạo nhân viên thành công!',
        okText:  'OK'
      });
      form.reset();
    }
    catch (err) {
      console.error(err);
      DraculaModal.show({
        title:   'Lỗi',
        message: err.message || 'Có lỗi xảy ra',
        okText:  'OK'
      });
    }
    finally {
      saveBtn.disabled    = false;
      saveBtn.textContent = 'Tạo nhân viên';
    }
  });
});
