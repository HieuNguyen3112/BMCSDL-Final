// public/js/fetchWithRefresh.js

/**
 * Wrapper thay thế fetch():
 * - tự động gắn Bearer token từ localStorage
 * - nếu gặp 401 sẽ tự gọi /api/refresh và retry một lần
 */
async function fetchWithRefresh(url, options = {}) {
    // luôn include cookie (session PHP)
    options.credentials = 'include';
  
    // chuẩn hóa headers
    options.headers = options.headers || {};
    if (!options.headers['Content-Type']) {
      options.headers['Content-Type'] = 'application/json';
    }
  
    // gắn access token nếu có
    const token = localStorage.getItem('token');
    if (token) {
      options.headers['Authorization'] = `Bearer ${token}`;
    }
  
    // gửi request chính
    let response = await fetch(url, options);
  
    // nếu token hết hạn (401), thử refresh
    if (response.status === 401) {
      const refreshToken = localStorage.getItem('refreshToken');
      if (!refreshToken) {
        // không có refreshToken thì redirect về login
        window.location.href = 'signin.php';
        return;
      }
  
      // gọi API cấp lại token
      const r = await fetch('/phpcoban/BMCSDL-Final/api/refresh', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ refreshToken })
      });
      const data = await r.json();
  
      if (r.ok && data.success) {
        // lưu lại token mới
        localStorage.setItem('token',        data.token);
        localStorage.setItem('refreshToken', data.refreshToken);
  
        // retry request gốc với token mới
        options.headers['Authorization'] = `Bearer ${data.token}`;
        response = await fetch(url, options);
      } else {
        // refresh không thành công → về login
        window.location.href = 'signin.php';
        return;
      }
    }
  
    return response;
  }
  
  // expose ra global để các file khác gọi
  window.fetchWithRefresh = fetchWithRefresh;
  