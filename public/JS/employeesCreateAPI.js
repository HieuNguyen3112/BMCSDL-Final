document.addEventListener('DOMContentLoaded', () => {
  const form    = document.getElementById('createEmpForm');
  const saveBtn = form.querySelector('button[type="submit"]');

  // Ánh xạ từ TenChucVu sang MaChucVu dựa trên dữ liệu thực tế
  const roleMapping = {
    'nhân viên': 1,
    'trưởng phòng': 2,
    'nhân viên phòng nhân sự': 3,
    'trưởng phòng nhân sự': 4,
    'nhân viên phòng tài vụ': 5,
    'giám đốc': 6
  };

  // Ánh xạ từ TenPhong sang MaPhong dựa trên dữ liệu thực tế
  const deptMapping = {
    'phòng nhân sự': 1,
    'phòng tài vụ': 2,
    'phòng giám đốc': 3,
    'phòng it': 4
  };

  form.addEventListener('submit', async e => {
    e.preventDefault();
    saveBtn.disabled    = true;
    saveBtn.textContent = 'Đang tạo...';

    // Xử lý ngày sinh
    let rawDate = form.NgaySinh.value.trim();
    let isoDate = rawDate; // Định dạng yyyy-mm-dd từ input type="date"

    // Chuẩn hóa và ánh xạ TenChucVu sang MaChucVu
    const tenChucVu = form.TenChucVu.value.trim().toLowerCase();
    const maChucVu = roleMapping[tenChucVu];
    if (!maChucVu) {
      saveBtn.disabled    = false;
      saveBtn.textContent = 'Tạo nhân viên';
      DraculaModal.show({
        title:   'Lỗi',
        message: `Chức vụ "${tenChucVu}" không hợp lệ. Vui lòng nhập: ${Object.keys(roleMapping).join(', ')}`,
        okText:  'OK'
      });
      return;
    }

    // Chuẩn hóa và ánh xạ TenPhong sang MaPhong
    const tenPhong = form.TenPhong.value.trim().toLowerCase();
    const maPhong = deptMapping[tenPhong];
    if (!maPhong) {
      saveBtn.disabled    = false;
      saveBtn.textContent = 'Tạo nhân viên';
      DraculaModal.show({
        title:   'Lỗi',
        message: `Phòng ban "${tenPhong}" không hợp lệ. Vui lòng nhập: ${Object.keys(deptMapping).join(', ')}`,
        okText:  'OK'
      });
      return;
    }

    // Tạo payload
    const payload = {
      HoTen:       form.HoTen.value.trim(),
      GioiTinh:    form.GioiTinh.value,
      NgaySinh:    isoDate,
      SoDienThoai: form.SoDienThoai.value.trim(),
      Luong:       form.Luong.value.trim().replace(/\./g,''),
      PhuCap:      form.PhuCap.value.trim().replace(/\./g,''),
      MaSoThue:    form.MaSoThue.value.trim(),
      MaChucVu:    maChucVu,
      MaPhong:     maPhong
    };

    // Ghi log để kiểm tra
    console.log('Payload gửi đi:', payload);

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