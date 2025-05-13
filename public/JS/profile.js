// profile.js – slide giữa 2 bước
document.addEventListener('DOMContentLoaded', () => {
    const form   = document.getElementById('emp-form');
    const slider = form.querySelector('.form-slider');
    const steps  = slider.children;
    const btnNext= form.querySelector('.btn-next');
    const btnPrev= form.querySelector('.btn-prev');
    let idx = 0;
  
    function updateButtons() {
      btnPrev.style.display = idx === 0 ? 'none' : 'flex';
      btnNext.style.display = idx === steps.length - 1 ? 'none' : 'flex';
    }
  
    function slideTo(i) {
      idx = i;
      slider.style.transform = `translateX(-${i * 50}%)`;
      updateButtons();
    }
  
    btnNext.addEventListener('click', () => {
      if (idx < steps.length - 1) slideTo(idx + 1);
    });
    btnPrev.addEventListener('click', () => {
      if (idx > 0) slideTo(idx - 1);
    });
  
    // Khởi tạo
    slideTo(0);
  });
  