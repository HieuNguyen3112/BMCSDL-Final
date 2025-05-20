// public/js/workspace.js
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('welcome-user');
    const name = localStorage.getItem('username') || 'Bạn';
    if (el) el.textContent = name;
  });
  