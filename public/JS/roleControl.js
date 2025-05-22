// public/JS/roleControl.js
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('infoModal');
    const modalBody = modal.querySelector('.modal-body');
    const closeBtn = document.getElementById('modalCloseBtn');
  
    closeBtn.addEventListener('click', () => {
      modal.classList.remove('open');
    });
  
    fetch('/phpcoban/BMCSDL-Final/api/profile', {
      headers: { 'Accept': 'application/json' }
    })
      .then(r => r.ok ? r.json() : Promise.reject())
      .then(body => {
        if (!body.success) return;
        const role = body.data.TenRole;
        console.log('Role hiện tại:', role);
  
        const waitForTable = setInterval(() => {
          const table = document.querySelector('.employees-table');
          if (!table) return;
          const headRow = table.querySelector('thead tr');
          const bodyRows = table.querySelectorAll('tbody tr');
          if (!headRow || bodyRows.length === 0) return;
  
          // Xác định index cột Thao tác
          const ths = Array.from(headRow.children);
          const actionIdx = ths.findIndex(th => th.classList.contains('action'));
  
          bodyRows.forEach(row => {
            const cells = Array.from(row.children);
            const maNV    = cells[1]?.textContent.trim();
            const hoTen   = cells[2]?.textContent.trim();
            const gioiTinh= cells[3]?.textContent.trim();
            const ngaySinh= cells[4]?.textContent.trim();
            const sdt     = cells[5]?.textContent.trim();
            const luong   = cells[6]?.textContent.trim();
            const phuCap  = cells[7]?.textContent.trim();
            const mst     = cells[8]?.textContent.trim();
            const chucVu  = cells[9]?.textContent.trim();
            const phong   = cells[10]?.textContent.trim();
  
            const btn = document.createElement('button');
            btn.className = 'action-btn';
  
            if (role === 'NhanVienRole') {
              btn.textContent = 'Xem';
              btn.addEventListener('click', () => {
                modalBody.innerHTML = `
                  <p><strong>Mã NV:</strong> ${maNV}</p>
                  <p><strong>Họ tên:</strong> ${hoTen}</p>
                  <p><strong>Giới tính:</strong> ${gioiTinh}</p>
                  <p><strong>Ngày sinh:</strong> ${ngaySinh}</p>
                  <p><strong>SDT:</strong> ${sdt}</p>
                  <p><strong>Lương:</strong> ${luong}</p>
                  <p><strong>Phụ cấp:</strong> ${phuCap}</p>
                  <p><strong>Mã số thuế:</strong> ${mst}</p>
                  <p><strong>Chức vụ:</strong> ${chucVu}</p>
                  <p><strong>Phòng:</strong> ${phong}</p>
                `;
                modal.classList.add('open');
              });
            } else {
              btn.textContent = 'Sửa';
              btn.addEventListener('click', () => {
                // Không gán onclick ở đây nữa, để editModal.js bắt click
              });
            }
  
            // Lấy cell action, nếu chưa có thì tạo
            let actionCell = cells[actionIdx];
            if (!actionCell) {
              actionCell = document.createElement('td');
              actionCell.classList.add('action');
              row.appendChild(actionCell);
            }
            actionCell.innerHTML = '';
            actionCell.appendChild(btn);
          });
  
          clearInterval(waitForTable);
        }, 100);
      })
      .catch(err => console.error('Lỗi khi kiểm tra role:', err));
  });
  