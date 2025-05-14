// public/js/employees.js

document.addEventListener('DOMContentLoaded', () => {
  const table = document.querySelector('.employees-table');
  if (!table) return;

  table.addEventListener('click', e => {
    const row = e.target.closest('tr');
    if (row && row.cells[1]) {
      alert(`Bạn đã chọn nhân viên: ${row.cells[1].textContent}`);
    }
  });
});
