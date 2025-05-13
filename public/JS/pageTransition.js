function fadeOutAndRedirect(url) {
    document.body.style.transition = 'opacity 0.3s ease';
    document.body.style.opacity = '0';
    setTimeout(() => {
      window.location.href = url;
    }, 600);
  }
  
  // Mặc định body opacity=1
  document.addEventListener('DOMContentLoaded', () => {
    document.body.style.opacity = '1';
  });
  