document.addEventListener('DOMContentLoaded', () => {
  const carousel = document.querySelector('[data-testimonials]');
  const track = document.querySelector('[data-testimonials-track]');
  if (!carousel || !track) return;

  const slides = Array.from(track.children);
  const prevButton = carousel.querySelector('.carousel-btn--prev');
  const nextButton = carousel.querySelector('.carousel-btn--next');
  let currentIndex = 0;
  let intervalId;

  const updateCarousel = () => {
    track.style.transform = `translateX(-${currentIndex * 100}%)`;
  };

  const goTo = (index) => {
    currentIndex = (index + slides.length) % slides.length;
    updateCarousel();
  };

  const startAutoplay = () => {
    intervalId = window.setInterval(() => goTo(currentIndex + 1), 5500);
  };

  const stopAutoplay = () => window.clearInterval(intervalId);

  prevButton?.addEventListener('click', () => goTo(currentIndex - 1));
  nextButton?.addEventListener('click', () => goTo(currentIndex + 1));
  carousel.addEventListener('mouseenter', stopAutoplay);
  carousel.addEventListener('mouseleave', startAutoplay);
  carousel.addEventListener('focusin', stopAutoplay);
  carousel.addEventListener('focusout', startAutoplay);

  updateCarousel();
  startAutoplay();
});
