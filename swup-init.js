document.addEventListener('DOMContentLoaded', () => {
  const init404Reveal = () => {
    if (!document.body.classList.contains('error404')) return;

    const image = document.getElementById('not-found-image');
    if (!image) {
      document.body.classList.remove('not-found-loading');
      document.body.classList.add('not-found-ready');
      return;
    }

    const reveal = () => {
      document.body.classList.remove('not-found-loading');
      document.body.classList.add('not-found-ready');
    };

    if (image.complete) {
      requestAnimationFrame(() => requestAnimationFrame(reveal));
      return;
    }

    image.addEventListener('load', reveal, { once: true });
    image.addEventListener('error', reveal, { once: true });
  };

  // Initialize Swup
  window.swup = new Swup({
    containers: ['#swup', '#primary-nav', '#featured-bg-layer'], // Update main content, nav, and background
    animationSelector: '[class*="transition-"]',
    cache: false,
    plugins: [
      new SwupProgressPlugin({
        className: 'swup-progress-bar',
        transition: 300,
        delay: 0,
        initialValue: 0.25,
        hideImmediately: true
      }),
      new SwupScrollPlugin({
        doScrollingRightAway: false,
        animateScroll: {
          betweenPages: true,
          samePageWithHash: true,
          samePage: true
        },
        offset: () => {
          // Compensate for the fixed header + some padding
          const header = document.querySelector('.site-header');
          const headerHeight = header ? header.offsetHeight : 55;
          // Add admin bar height if present
          const adminBar = document.getElementById('wpadminbar');
          const adminBarHeight = adminBar ? adminBar.offsetHeight : 0;
          return headerHeight + adminBarHeight + 20;
        }
      }),
      new SwupBodyClassPlugin(),
      new SwupHeadPlugin()
    ]
  });

  // Wait for images to load before completing the transition
  window.swup.hooks.on('content:replace', async () => {
    // 1. In articles, focus on the background image loading first
    const imagesToWaitFor = [];
    
    // Check all elements in the featured background layer (it updates via Swup too)
    const bgElements = Array.from(document.querySelectorAll('#featured-bg-layer, #featured-bg-layer *'));
    bgElements.forEach(el => {
      const style = window.getComputedStyle(el);
      const bgImgUrl = style.backgroundImage;
      if (bgImgUrl && bgImgUrl !== 'none') {
        const urlMatch = bgImgUrl.match(/url\(["']?([^"']+)["']?\)/);
        if (urlMatch && urlMatch[1]) {
          const img = new Image();
          img.src = urlMatch[1];
          imagesToWaitFor.push(img);
        }
      }
    });

    // You can add logic to wait for more images if you want on other pages,
    // but the user specifically asked for articles to show text as soon as the background is ready.
    if (imagesToWaitFor.length === 0) return;

    // Wait for critical background images to load
    await Promise.all(
      imagesToWaitFor.map(img => {
        if (img.complete) return Promise.resolve();
        return new Promise(resolve => {
          const finish = () => {
            img.onload = img.onerror = null;
            resolve();
          };
          img.onload = finish;
          img.onerror = finish;
          // Shorter fallback (1500ms) to keep navigation snappy
          setTimeout(finish, 1500);
        });
      })
    );
  });

  // Re-initialize scripts after page transition
  window.swup.hooks.on('page:view', () => {
    // Remove has-replied class to allow animations on next page load
    document.body.classList.remove('has-replied');

    // Re-run code copy initialization if it exists
    if (typeof window.initCodeCopy === 'function') {
      window.initCodeCopy();
    }
    
    // Re-run safari fix if needed
    if (typeof window.applySafariFix === 'function') {
      window.applySafariFix();
    }

    // Re-initialize comments logic (fragment loading)
    if (typeof window.initComments === 'function') {
      window.initComments();
    }

    // Re-initialize friend link modal logic (moved here if separate)
    if (typeof window.initFriendModal === 'function') {
      window.initFriendModal();
    }

    // Re-initialize TOC logic
    if (typeof window.initToc === 'function') {
      window.initToc();
    }

    // Ensure 404 reveal animation also works on Swup navigation.
    init404Reveal();
  });

  // Initial full page load.
  init404Reveal();
});
