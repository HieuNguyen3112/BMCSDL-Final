// auditLogFetchAPI.js

document.addEventListener('DOMContentLoaded', () => {
  const tbody = document.getElementById('auditlog-body');
  if (!tbody) return;

  // Bản đồ nhãn hành động
  const actionLabels = {
    VIEW_PROFILE:      'Xem thông tin cá nhân',
    VIEW_LIST:         'Xem danh sách nhân viên',
    LOGIN:             'Đăng nhập',
    LOGOUT:            'Đăng xuất',
    CREATE_EMPLOYEE:   'Thêm nhân viên',
    UPDATE_EMPLOYEE:   'Chỉnh sửa nhân viên',
  };

  fetch('/phpcoban/BMCSDL-Final/api/audit-log', {
    headers:     { 'Accept': 'application/json' },
    credentials: 'include'
  })
    .then(res => res.ok ? res.json() : Promise.reject(res))
    .then(({ success, data }) => {
      if (!success) return;

      // 1) Lọc bỏ logout giả (user_id = 0)
      // 2) Lọc bỏ VIEW_PROFILE khi user tự xem profile của mình
      const filtered = data.filter(log => {
        // loại bỏ logout giả
        if (log.action === 'LOGOUT' && Number(log.user_id) === 0) {
          return false;
        }
        // loại bỏ tự xem profile
        if (log.action === 'VIEW_PROFILE' 
            && Number(log.user_id) === Number(log.record_id)) {
          return false;
        }
        return true;
      });

      filtered.forEach((log, idx) => {
        const time      = log.created_at  || '';
        const user      = log.user_name   || '';
        const rawAction = log.action      || '';
        const action    = actionLabels[rawAction] || rawAction;

        let detailText = '';

        // Hàm parse JSON an toàn
        const safeParse = str => {
          try { return JSON.parse(str); }
          catch { return null; }
        };

        switch (rawAction) {
          case 'VIEW_PROFILE': {
            const obj  = safeParse(log.new_data);
            const name = obj?.HoTen || '';
            detailText = `${user} đã xem thông tin cá nhân của ${name}`;
            break;
          }
          case 'VIEW_LIST': {
            detailText = `${user} đã xem danh sách nhân viên`;
            break;
          }
          case 'LOGIN': {
            detailText = `${user} đã đăng nhập`;
            break;
          }
          case 'LOGOUT': {
            detailText = `${user} đã đăng xuất`;
            break;
          }
          case 'CREATE_EMPLOYEE': {
            const obj  = safeParse(log.new_data);
            const name = obj?.HoTen || '';
            detailText = `${user} đã thêm nhân viên ${name}`;
            break;
          }
          case 'UPDATE_EMPLOYEE': {
            const oldObj = safeParse(log.old_data);
            const newObj = safeParse(log.new_data);
            const changes = [];

            ['HoTen','SoDienThoai','Luong','PhuCap','MaSoThue','TenChucVu','TenPhong']
              .forEach(field => {
                const o = oldObj?.[field] ?? '';
                const n = newObj?.[field] ?? '';
                if (o !== n) {
                  changes.push(`${field}: ${o} → ${n}`);
                }
              });

            const empName = newObj?.HoTen || '';
            detailText = `${user} đã chỉnh sửa nhân viên ${empName}`;
            if (changes.length) {
              detailText += ` (${changes.join('; ')})`;
            }
            break;
          }
          default: {
            detailText = `${user || 'Người dùng'} thực hiện “${action}” trên bảng ${log.table_name} (ID: ${log.record_id})`;
          }
        }

        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${idx + 1}</td>
          <td>${time}</td>
          <td>${user || '-'}</td>
          <td>${action}</td>
          <td>${detailText}</td>
        `;
        tbody.appendChild(tr);
      });
    })
    .catch(err => console.error('Lỗi khi fetch audit-log:', err));
});
