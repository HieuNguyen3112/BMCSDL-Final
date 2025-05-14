// public/js/transition.js
(function(){
  const D = 800;
  const html = document.documentElement;
  html.style.transformOrigin = 'center center';
  html.style.transition = `transform ${D}ms ease, opacity ${D}ms ease`;
  html.style.transform = 'scale(0.9)';
  html.style.opacity   = '0';

  window.addEventListener('load', () => {
    setTimeout(()=>{
      html.style.transform = 'scale(1)';
      html.style.opacity   = '1';
    }, 20);
  });

  document.addEventListener('click', e => {
    const a = e.target.closest('a');
    if (!a) return;
    const href = a.href;
    // skip external/blank/anchors/logout
    if (
      a.target === '_blank' ||
      !href.startsWith(location.origin) ||
      href.includes('controller=logout') ||
      href.startsWith('#') ||
      href.startsWith('javascript:')
    ) return;

    e.preventDefault();
    html.style.transform = 'scale(1.1)';
    html.style.opacity   = '0';
    setTimeout(()=>window.location.href = href, D);
  });

  window.addEventListener('pageshow', evt => {
    if (evt.persisted) {
      html.style.transform = 'scale(1)';
      html.style.opacity   = '1';
    }
  });
})();
