// public/js/editModal.js
document.addEventListener('DOMContentLoaded', () => {
    const editModal = document.getElementById('editModal');
    const closeBtn  = document.getElementById('editModalCloseBtn');
    const form      = document.getElementById('editEmpForm');
  
    // 1) Bắt click vào nút "Sửa"
    document.querySelector('.employees-table').addEventListener('click', e => {
      // kiểm tra đúng nút Sửa
      if (!e.target.classList.contains('action-btn') || e.target.textContent.trim() !== 'Sửa') 
        return;
  
      // tìm row và cells
      const row   = e.target.closest('tr');
      const cells = Array.from(row.children);
  
      // đổ data
      form.elements['HoTen'].value     = cells[2].textContent.trim();
      form.elements['GioiTinh'].value  = cells[3].textContent.trim();
      form.elements['NgaySinh'].value  = (() => {
        const [d,m,y] = cells[4].textContent.trim().split('/');
        return `${y}-${m.padStart(2,'0')}-${d.padStart(2,'0')}`;
      })();
      form.elements['SoDienThoai'].value = cells[5].textContent.trim();
      form.elements['Luong'].value       = cells[6].textContent.trim().replace(/\./g,'');
      form.elements['PhuCap'].value      = cells[7].textContent.trim().replace(/\./g,'');
      form.elements['MaSoThue'].value    = cells[8].textContent.trim();
      form.elements['TenChucVu'].value   = cells[9].textContent.trim();
      form.elements['TenPhong'].value    = cells[10].textContent.trim();
  
      // mở modal
      editModal.classList.add('open');
    });
  
    // 2) Đóng modal khi bấm ×
    closeBtn.addEventListener('click', () => {
      editModal.classList.remove('open');
    });
  
    // 3) Ngăn form submit (chờ bạn gắn API sau)
    form.addEventListener('submit', e => {
      e.preventDefault();
      // … chờ gắn API ở bước sau …
    });
});
  