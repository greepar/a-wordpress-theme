const initCodeCopyButtons = () => {
  const blocks = document.querySelectorAll(
    'pre.wp-block-code, pre[class*="language-"]'
  );

  blocks.forEach((pre) => {
    if (pre.querySelector('.copy-code')) {
      return;
    }

    const code = pre.querySelector('code');
    if (!code) {
      return;
    }

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'copy-code';
    button.textContent = 'copy';
    button.setAttribute('aria-label', 'Copy code');

    button.addEventListener('click', () => {
      const text = code.textContent || '';
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(
          () => {
            button.textContent = 'copied';
            setTimeout(() => {
              button.textContent = 'copy';
            }, 1500);
          },
          () => {
            button.textContent = 'failed';
            setTimeout(() => {
              button.textContent = 'copy';
            }, 1500);
          }
        );
      } else {
        button.textContent = 'failed';
        setTimeout(() => {
          button.textContent = 'copy';
        }, 1500);
      }
    });

    pre.appendChild(button);
  });
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initCodeCopyButtons);
} else {
  initCodeCopyButtons();
}

const observer = new MutationObserver(() => {
  initCodeCopyButtons();
});

observer.observe(document.body, { childList: true, subtree: true });
