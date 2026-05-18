document.addEventListener('DOMContentLoaded', () => {
  const logo = document.querySelector('.site-logo-img');
  if (!logo) return;

  const hoverSrc = logo.getAttribute('data-hover-src');
  if (!hoverSrc) return;

  const originalSrc = logo.src;

  // Preload the hover image
  const img = new Image();
  img.src = hoverSrc;

  logo.addEventListener('mouseenter', () => {
    logo.src = hoverSrc;
  });

  logo.addEventListener('mouseleave', () => {
    logo.src = originalSrc;
  });
});
