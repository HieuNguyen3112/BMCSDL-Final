// roleCheck.js
;(function(){
    if (!Array.isArray(window.ALLOWED_ROLES)) {
      console.error('ALLOWED_ROLES chưa được định nghĩa');
      return;
    }
  
    fetch('/phpcoban/BMCSDL-Final/api/profile', {
      headers: { 'Accept': 'application/json' },
      credentials: 'include'
    })
    .then(res => res.ok ? res.json() : Promise.reject(res))
    .then(body => {
      if (!body.success) return;
      const role = body.data.TenRole;
      if (!window.ALLOWED_ROLES.includes(role)) {
        alert('Bạn không có quyền truy cập trang này.');
        window.location.replace('employees.php');
      }
    })
    .catch(err => console.error('Lỗi kiểm tra quyền:', err));
  })();
  