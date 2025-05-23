// public/js/loginAPI.js

document.getElementById('login-form').addEventListener('submit', async e => {
  e.preventDefault();

  const maNV     = e.target.maNV.value.trim();
  const password = e.target.password.value.trim();
  const role     = e.target.role ? e.target.role.value : null;  // nếu có select role

  try {
    // Gọi fetchWithRefresh từ global window
    const resp = await window.fetchWithRefresh(
      '/phpcoban/BMCSDL-Final/api/login',
      {
        method: 'POST',
        headers: { 'Content-Type':'application/json' },
        body: JSON.stringify({ maNV, password, role })
      }
    );

    // Đọc nguyên text để bắt lỗi JSON “bẩn”
    const text = await resp.text();
    let data;
    try {
      data = JSON.parse(text);
    } catch {
      console.error('Invalid JSON response:', text);
      throw new Error('Server trả về không đúng định dạng JSON');
    }

    if (!resp.ok || !data.success) {
      throw new Error(data.message || 'Đăng nhập thất bại');
    }

    // Lưu token + refreshToken
    localStorage.setItem('token',        data.token);
    localStorage.setItem('refreshToken', data.refreshToken);

    DraculaModal.show({
      title:   'Đăng nhập thành công',
      message: 'Chuyển trang sau 1s…',
      okText:  'OK'
    });

    setTimeout(() => window.location.href = 'profile.php', 1000);

  } catch (err) {
    console.error('Login error:', err);
    DraculaModal.show({ title:'Lỗi kết nối', message: err.message, okText:'OK' });
  }
});
