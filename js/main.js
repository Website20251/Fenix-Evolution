document.addEventListener('DOMContentLoaded', () => {
  const header = document.querySelector('.site-header');
  const nav = document.querySelector('.site-nav');
  const toggle = document.querySelector('.nav-toggle');
  const navLinks = document.querySelectorAll('.site-nav a');
  const reveals = document.querySelectorAll('.reveal');
  const progressBar = document.querySelector('.scroll-progress span');
  const scrollTopButton = document.querySelector('.scroll-top');
  const lightbox = document.getElementById('lightbox');
  const lightboxImage = document.querySelector('.lightbox__image');
  const lightboxClose = document.querySelector('.lightbox__close');
  const galleryItems = document.querySelectorAll('[data-lightbox-src]');
  const contactForm = document.getElementById('contactForm');
  const formFeedback = document.getElementById('formFeedback');

  const setScrollState = () => {
    const scrolled = window.scrollY > 24;
    header.classList.toggle('is-scrolled', scrolled);
    scrollTopButton.classList.toggle('is-visible', window.scrollY > 500);
    const height = document.documentElement.scrollHeight - window.innerHeight;
    const progress = height > 0 ? (window.scrollY / height) * 100 : 0;
    progressBar.style.width = `${progress}%`;
  };

  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        revealObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.18 });
  reveals.forEach((node) => revealObserver.observe(node));

  const sectionObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      navLinks.forEach((link) => {
        link.classList.toggle('is-active', link.getAttribute('href') === `#${entry.target.id}`);
      });
    });
  }, { threshold: 0.3 });
  document.querySelectorAll('section[id]').forEach((section) => sectionObserver.observe(section));

  toggle.addEventListener('click', () => {
    const isOpen = nav.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', String(isOpen));
  });

  navLinks.forEach((link) => link.addEventListener('click', () => {
    nav.classList.remove('is-open');
    toggle.setAttribute('aria-expanded', 'false');
  }));

  scrollTopButton.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  const closeLightbox = () => {
    lightbox.classList.remove('is-open');
    lightbox.setAttribute('aria-hidden', 'true');
    lightboxImage.src = '';
    document.body.style.overflow = '';
  };

  galleryItems.forEach((item) => item.addEventListener('click', () => {
    lightboxImage.src = item.getAttribute('data-lightbox-src');
    lightboxImage.alt = item.getAttribute('data-lightbox-alt') || '';
    lightbox.classList.add('is-open');
    lightbox.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }));

  lightboxClose.addEventListener('click', closeLightbox);
  lightbox.addEventListener('click', (event) => { if (event.target === lightbox) closeLightbox(); });
  document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && lightbox.classList.contains('is-open')) closeLightbox(); });

  if (contactForm) {
    contactForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      formFeedback.textContent = '';

      if (!contactForm.checkValidity()) {
        contactForm.reportValidity();
        formFeedback.textContent = 'Revisa los campos marcados antes de enviar.';
        formFeedback.style.color = '#ffb4b4';
        return;
      }

      try {
        const response = await fetch(contactForm.action, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
          body: new URLSearchParams(new FormData(contactForm))
        });
        const data = await response.json();

        if (!response.ok || !data.success) {
          throw new Error(data.message || 'No fue posible enviar el formulario.');
        }

        contactForm.reset();
        formFeedback.textContent = data.message;
        formFeedback.style.color = '#a0ff32';
      } catch (error) {
        formFeedback.textContent = error.message;
        formFeedback.style.color = '#ffb4b4';
      }
    });
  }

  setScrollState();
  window.addEventListener('scroll', setScrollState, { passive: true });
});
