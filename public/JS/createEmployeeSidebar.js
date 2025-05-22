// public/js/createEmployeeSidebar.js
document.addEventListener('DOMContentLoaded', () => {
    fetch('/phpcoban/BMCSDL-Final/api/profile', {
      headers: { 'Accept': 'application/json' },
      credentials: 'include',
    })
      .then(r => r.ok ? r.json() : Promise.reject())
      .then(body => {
        if (!body.success) return;
        const role = body.data.TenRole;
        // Chỉ cho phép các role này nhìn thấy link Tạo mới nhân viên
        const allowed = [
          'TruongPhongRole',
          'NhanVienNhanSuRole',
          'NhanVienTaiVuRole',
          'GiamDocRole'
        ];
        if (!allowed.includes(role)) {
          const li = document.getElementById('nav-create-employee');
          if (li) li.style.display = 'none';
        }
      })
      .catch(err => console.error('Lỗi kiểm tra quyền tạo nhân viên:', err));
  });
  