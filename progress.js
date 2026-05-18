document.addEventListener('DOMContentLoaded', () => {
  const bar = document.getElementById('page-progress');
  if (!bar) {
    return;
  }

  let showTimeout;

  const showBar = (width) => {
    // If already visible, update immediately
    if (bar.classList.contains('is-active')) {
      bar.style.width = width;
      return;
    }

    // Otherwise, delay showing
    if (showTimeout) clearTimeout(showTimeout);
    showTimeout = setTimeout(() => {
      bar.classList.add('is-active');
      requestAnimationFrame(() => {
        bar.style.width = width;
      });
    }, 200); // 200ms threshold
  };

  const finish = () => {
    if (showTimeout) clearTimeout(showTimeout);

    // Only animate finish if it's visible
    if (bar.classList.contains('is-active')) {
      bar.style.width = '100%';
      setTimeout(() => {
        bar.classList.remove('is-active');
        setTimeout(() => {
          bar.style.width = '0%';
        }, 200);
      }, 400); // Disappear faster (400ms)
    }
  };

  // Initialize on page load
  showBar('30%');

  document.addEventListener('click', (event) => {
    if (event.defaultPrevented) return;
    const link = event.target.closest('a');
    if (!link || link.target === '_blank' || link.hasAttribute('download')) {
      return;
    }
    const href = link.getAttribute('href') || '';
    // 跳过锚点链接（包括完整URL中的锚点）
    if (href.startsWith('#') || (href.includes('#') && link.origin === window.location.origin && link.pathname === window.location.pathname)) {
      return;
    }
    if (href.startsWith('javascript:')) {
      return;
    }
    // 跳过外链
    if (link.origin !== window.location.origin) {
      return;
    }
    
    // 如果启用了 Swup (PJAX)，让 Swup 自己的进度条插件接管，这里不再显示原生进度条
    if (window.swup) {
      return;
    }

    showBar('85%');
  });

  window.addEventListener('beforeunload', () => showBar('85%'));
  window.addEventListener('load', finish);

  // 处理返回上一页时的进度条残留（bfcache）
  window.addEventListener('pageshow', (event) => {
    // 无论是否是从缓存读取，都确保进度条在页面完全显示后消失
    finish();
  });
});
