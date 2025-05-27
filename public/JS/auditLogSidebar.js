// public/js/auditLogSidebar.js
document.addEventListener('DOMContentLoaded', () => {
    fetch('/phpcoban/BMCSDL-Final/api/profile', {
      headers: { 'Accept': 'application/json' },
      credentials: 'include'
    })
      .then(r => r.ok ? r.json() : Promise.reject(r))
      .then(body => {
        if (!body.success) return;
        const role = body.data.TenRole;
        const allowed = [
          'GiamDocRole',
          'TruongPhongNhanSuRole',
        ];
        const li = document.getElementById('nav-audit-log');
        if (li) {
          li.style.display = allowed.includes(role) ? '' : 'none';
        }
      })
      .catch(err => console.error('Lỗi kiểm tra quyền Audit Log:', err));
  });
  