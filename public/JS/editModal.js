// public/js/editModal.js
document.addEventListener('DOMContentLoaded', () => {
  const editModal = document.getElementById('editModal');
  const closeBtn  = document.getElementById('editModalCloseBtn');
  const form      = document.getElementById('editEmpForm');

  document.querySelector('.employees-table').addEventListener('click', e => {
    // CHỈ BẮT button[data-action="edit"]
    const btn = e.target.closest('button[data-action="edit"]');
    if (!btn) return;

    // highlight
    const row = btn.closest('tr');
    document.querySelectorAll('.employees-table tr').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');

    // đổ data vào form
    const cells = row.children;
    form.elements['HoTen'].value      = cells[2].textContent.trim();
    form.elements['GioiTinh'].value   = cells[3].textContent.trim();
    // chuyển dd/MM/YYYY → YYYY-MM-DD
    const [d,m,y] = cells[4].textContent.trim().split('/');
    form.elements['NgaySinh'].value   = `${y}-${m.padStart(2,'0')}-${d.padStart(2,'0')}`;
    form.elements['SoDienThoai'].value= cells[5].textContent.trim();
    form.elements['Luong'].value      = cells[6].textContent.trim().replace(/\./g,'');
    form.elements['PhuCap'].value     = cells[7].textContent.trim().replace(/\./g,'');
    form.elements['MaSoThue'].value   = cells[8].textContent.trim();
    form.elements['TenChucVu'].value  = cells[9].textContent.trim();
    form.elements['TenPhong'].value   = cells[10].textContent.trim();

    editModal.classList.add('open');
  });

  closeBtn.addEventListener('click', () => editModal.classList.remove('open'));
});
