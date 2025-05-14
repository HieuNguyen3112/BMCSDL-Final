// public/js/transition.js
(function(){
    const DURATION = 600; 
    const wrapper = document.querySelector('.main-content');
    if (!wrapper) return;
  
    // 1. Thiết lập transition
    wrapper.style.transition = 
      `transform ${DURATION}ms ease, opacity ${DURATION}ms ease`;
    wrapper.style.opacity   = '0';
    wrapper.style.transform = 'scale(0.9)';
  
    // 2. Zoom-out + fade-in khi DOM sẵn sàng
    window.addEventListener('DOMContentLoaded', () => {
      setTimeout(() => {
        wrapper.style.opacity   = '1';
        wrapper.style.transform = 'scale(1)';
      }, 20);
    });
  
    // 3. Hàm reload full (dùng cho logout, hoặc link muốn full reload)
    function fullReload(url) {
      window.location.href = url;
    }
  
    // 4. Hàm fetch & replace nội dung (AJAX navigation)
    function ajaxNavigate(url, addToHistory = true) {
      fetch(url, { credentials: 'same-origin' })
        .then(r => r.text())
        .then(htmlString => {
          const doc = new DOMParser().parseFromString(htmlString, 'text/html');
          const newWrapper = doc.querySelector('.main-content');
          if (!newWrapper) return fullReload(url);
  
          // Thay nội dung
          wrapper.innerHTML = newWrapper.innerHTML;
          document.title   = doc.title;
          if (addToHistory) history.pushState(null, '', url);
  
          // Zoom-out + fade-in phần nội dung mới
          wrapper.style.opacity   = '1';
          wrapper.style.transform = 'scale(1)';
  
          // Re-initialize mọi script cần thiết
          if (window.reinitializePage) window.reinitializePage();
        })
        .catch(() => fullReload(url));
    }
  
    // 5. Intercept click trên <a>
    document.addEventListener('click', e => {
      const a = e.target.closest('a');
      if (!a) return;
      const href = a.href;
  
      // Các trường hợp full reload:
      if (
        a.hasAttribute('data-fullreload') ||                // bạn có thể đánh dấu link
        href.includes('controller=logout') ||               // logout
        a.target === '_blank' ||
        href.indexOf(location.origin) !== 0 ||
        href.startsWith('#') ||
        href.startsWith('javascript:')
      ) {
        e.preventDefault();
        return fullReload(href);
      }
  
      // Mọi link còn lại: AJAX navigation
      e.preventDefault();
      // Zoom-in + fade-out
      wrapper.style.opacity   = '0';
      wrapper.style.transform = 'scale(1.1)';
  
      setTimeout(() => ajaxNavigate(href), DURATION);
    });
  
    // 6. Back/forward support
    window.addEventListener('popstate', () => {
      // Lặp lại AJAX navigate mà không pushHistory
      wrapper.style.opacity   = '0';
      wrapper.style.transform = 'scale(1.1)';
      setTimeout(() => ajaxNavigate(location.href, false), DURATION);
    });
  })();
  