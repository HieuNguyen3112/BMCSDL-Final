// profileUpdate.js
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('emp-form');
  if (!form) return;

  form.addEventListener('submit', async e => {
    e.preventDefault();
    const token = localStorage.getItem('token');

    const payload = {
      HoTen:       document.getElementById('emp_name').value.trim(),
      GioiTinh:    document.querySelector('input[name="gender"]').value.trim(),
      NgaySinh:    document.getElementById('dob').value.trim(),
      SoDienThoai: document.getElementById('phone').value.trim(),
      Luong:       document.getElementById('salary').value.trim(),
      PhuCap:      document.getElementById('allowance').value.trim(),
      MaSoThue:    document.getElementById('tax_id').value.trim(),
      MaChucVu:    document.getElementById('position').value.trim(),
      MaPhong:     document.getElementById('department').value.trim()
    };

    console.log('Payload gửi đi:', payload); // ✅ Log payload

    try {
      const resp = await fetch('/phpcoban/BMCSDL-Final/api/profile', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          ...(token && { 'Authorization': `Bearer ${token}` })
        },
        body: JSON.stringify(payload)
      });
      const result = await resp.json();
      console.log('Kết quả cập nhật:', result); // ✅ Log kết quả

      if (!resp.ok || !result.success) {
        console.error('API lỗi:', result.error || result.message);
        DraculaModal.show({
          title: 'Cập nhật thất bại',
          message: result.error || 'Không thể lưu thông tin.',
          okText: 'OK'
        });
        return;
      }

      DraculaModal.show({
        title: 'Cập nhật thành công',
        message: 'Thông tin cá nhân đã được lưu.',
        okText: 'OK'
      });
    } catch (err) {
      console.error('Lỗi kết nối:', err);
      DraculaModal.show({
        title: 'Lỗi kết nối',
        message: 'Không thể kết nối tới server.',
        okText: 'OK'
      });
    }
  });
});
