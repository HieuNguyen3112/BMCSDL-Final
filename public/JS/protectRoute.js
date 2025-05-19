// public/JS/protectRoute.js
(function() {
  // Danh sách trang công khai
  const whiteList = new Set(['', 'index.php', 'signin.php']);

  // Hàm kiểm tra token
  function checkAuth() {
    const raw = window.location.pathname.split('/').pop();
    const filename = raw.split(/[?#]/)[0];
    if (whiteList.has(filename)) {
      showBody();
      return;
    }
    if (!localStorage.getItem('token')) {
      window.location.replace('signin.php');
    } else {
      showBody();
    }
  }

  // Hiện lại body khi đã xác thực
  function showBody() {
    const style = document.getElementById('protect-style');
    if (style) style.parentNode.removeChild(style);
    document.body.style.display = '';
  }

  // Chạy kiểm tra ngay lập tức
  checkAuth();

  // Khi page được load từ bfcache, cũng kiểm tra lại
  window.addEventListener('pageshow', (e) => {
    if (e.persisted) {
      checkAuth();
    }
  });
})();
