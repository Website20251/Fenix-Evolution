document.addEventListener('DOMContentLoaded', () => {
  const heroBackdrop = document.querySelector('.hero__backdrop');
  const nav = document.querySelector('.site-nav');
  const toggle = document.querySelector('.nav-toggle');

  const parallax = () => {
    if (!heroBackdrop) return;
    const offset = window.scrollY * 0.16;
    heroBackdrop.style.transform = `scale(1.04) translateY(${offset * 0.12}px)`;
  };

  const resetNav = () => {
    if (window.innerWidth > 960 && nav) {
      nav.classList.remove('is-open');
      toggle?.setAttribute('aria-expanded', 'false');
    }
  };

  parallax();
  window.addEventListener('scroll', parallax, { passive: true });
  window.addEventListener('resize', resetNav);
});
