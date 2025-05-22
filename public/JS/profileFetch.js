// profileFetch.js
document.addEventListener('DOMContentLoaded', async () => {
  const token = localStorage.getItem('token');

  try {
    const resp = await fetch('/phpcoban/BMCSDL-Final/api/profile', {
      headers: {
        'Accept': 'application/json',
        ...(token && { 'Authorization': `Bearer ${token}` })
      }
    });
    const result = await resp.json();

    if (!resp.ok || !result.success) {
      console.error('API lỗi:', result.error || result.message);
      DraculaModal.show({
        title: 'Lỗi tải thông tin',
        message: result.error || 'Không thể lấy dữ liệu cá nhân.',
        okText: 'OK'
      });
      return;
    }

    const data = result.data;
    document.getElementById('emp_id').value     = data.MaNhanVien;
    document.getElementById('emp_name').value   = data.HoTen;
    document.querySelector('input[name="gender"]').value = data.GioiTinh;
    document.getElementById('dob').value        = data.NgaySinh;
    document.getElementById('phone').value      = data.SoDienThoai;
    document.getElementById('salary').value     = data.Luong;
    document.getElementById('allowance').value  = data.PhuCap;
    document.getElementById('tax_id').value     = data.MaSoThue;
    document.getElementById('position').value   = data.TenChucVu;
    document.getElementById('department').value = data.TenPhong;
  } catch (err) {
    console.error('Lỗi kết nối:', err);
    DraculaModal.show({
      title: 'Lỗi kết nối',
      message: 'Không thể kết nối tới server.',
      okText: 'OK'
    });
  }
});
