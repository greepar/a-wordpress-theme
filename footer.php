</main>

<footer class="site-footer">
  <div class="container">
    <p>
      <?php
      $default_footer = '&copy; {year} greepar.uk &bull; {site_name} &bull; <a href="https://icp.gov.moe/?keyword=20261233" target="_blank" style="color: inherit; text-decoration: none;">萌ICP备20261233号</a>';
      $footer_text = get_theme_mod('footer_text', $default_footer);
      $footer_text = str_replace('{year}', date('Y'), $footer_text);
      $footer_text = str_replace('{site_name}', esc_html(get_bloginfo('name')), $footer_text);
      echo wp_kses_post($footer_text);
      ?>
    </p>
  </div>
</footer>
</div> <!-- End #swup -->

<button class="theme-mode-toggle" type="button" aria-label="切换亮暗模式" title="当前：跟随系统，点击切换" data-mode="system">
  <span class="theme-mode-toggle-icon theme-mode-toggle-icon-system" aria-hidden="true">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M12 3v2"></path>
      <path d="M12 19v2"></path>
      <path d="m4.93 4.93 1.41 1.41"></path>
      <path d="m17.66 17.66 1.41 1.41"></path>
      <path d="M3 12h2"></path>
      <path d="M19 12h2"></path>
      <path d="m6.34 17.66-1.41 1.41"></path>
      <path d="m19.07 4.93-1.41 1.41"></path>
      <path d="M12 7a5 5 0 1 0 5 5"></path>
      <path d="M12 7a5 5 0 0 1 0 10"></path>
    </svg>
  </span>
  <span class="theme-mode-toggle-icon theme-mode-toggle-icon-light" aria-hidden="true">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="12" cy="12" r="4"></circle>
      <path d="M12 2v2"></path>
      <path d="M12 20v2"></path>
      <path d="m4.93 4.93 1.41 1.41"></path>
      <path d="m17.66 17.66 1.41 1.41"></path>
      <path d="M2 12h2"></path>
      <path d="M20 12h2"></path>
      <path d="m6.34 17.66-1.41 1.41"></path>
      <path d="m19.07 4.93-1.41 1.41"></path>
    </svg>
  </span>
  <span class="theme-mode-toggle-icon theme-mode-toggle-icon-dark" aria-hidden="true">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M12 3a6 6 0 1 0 9 9 9 9 0 1 1-9-9z"></path>
    </svg>
  </span>
  <span class="theme-mode-toggle-badge" aria-hidden="true">A</span>
</button>

<button class="back-to-top" aria-label="Back to top">
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M18 15l-6-6-6 6"/>
  </svg>
</button>

<script>
  (function() {
    var backToTop = document.querySelector('.back-to-top');
    var themeToggle = document.querySelector('.theme-mode-toggle');

    if (!backToTop) return;

    function syncFloatingButtons() {
      var isVisible = window.scrollY > 300;
      backToTop.classList.toggle('is-visible', isVisible);
      if (themeToggle) {
        themeToggle.classList.toggle('is-visible', isVisible);
      }
    }

    window.addEventListener('scroll', function() {
      syncFloatingButtons();
    });

    backToTop.addEventListener('click', function() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });

    syncFloatingButtons();
  })();
</script>

<script>
  (function() {
    var toggle = document.querySelector('.theme-mode-toggle');
    var root = document.documentElement;
    var storageKey = 'chickensoft-theme-mode';
    var modes = ['system', 'light', 'dark'];
    var labels = {
      system: '跟随系统',
      light: '亮色模式',
      dark: '暗色模式'
    };
    var badges = {
      system: 'A',
      light: 'L',
      dark: 'D'
    };
    var media = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

    if (!toggle) return;

    function getStoredMode() {
      try {
        var stored = localStorage.getItem(storageKey);
        if (modes.indexOf(stored) !== -1) {
          return stored;
        }
      } catch (error) {}
      return 'system';
    }

    function applyMode(mode) {
      if (mode === 'light' || mode === 'dark') {
        root.setAttribute('data-theme', mode);
      } else {
        root.removeAttribute('data-theme');
      }

      root.setAttribute('data-theme-mode', mode);
      toggle.setAttribute('data-mode', mode);
      toggle.setAttribute('aria-label', '切换亮暗模式，当前：' + labels[mode]);
      toggle.setAttribute('title', '当前：' + labels[mode] + '，点击切换');

      var badge = toggle.querySelector('.theme-mode-toggle-badge');
      if (badge) {
        badge.textContent = badges[mode];
      }
    }

    function persistMode(mode) {
      try {
        if (mode === 'system') {
          localStorage.removeItem(storageKey);
        } else {
          localStorage.setItem(storageKey, mode);
        }
      } catch (error) {}
    }

    toggle.addEventListener('click', function() {
      var currentMode = root.getAttribute('data-theme-mode') || getStoredMode();
      var currentIndex = modes.indexOf(currentMode);
      var nextMode = modes[(currentIndex + 1) % modes.length];
      persistMode(nextMode);
      applyMode(nextMode);
    });

    if (media) {
      var handleSystemChange = function() {
        if ((root.getAttribute('data-theme-mode') || 'system') === 'system') {
          applyMode('system');
        }
      };

      if (media.addEventListener) {
        media.addEventListener('change', handleSystemChange);
      } else if (media.addListener) {
        media.addListener(handleSystemChange);
      }
    }

    applyMode(getStoredMode());
  })();
</script>

<?php wp_footer(); ?>
<script>
  (function () {
    var menuParents = document.querySelectorAll('.primary-nav li.menu-item-has-children > a');
    menuParents.forEach(function (link) {
      link.addEventListener('click', function (event) {
        if (window.matchMedia('(max-width: 900px)').matches) {
          var parent = link.parentElement;
          if (link.getAttribute('href') === '#') {
            event.preventDefault();
          }
          parent.classList.toggle('open');
        }
      });
    });
  })();
</script>
<script>
  (function () {
    var toggle = document.querySelector('.header-menu-toggle');
    var headerActions = document.querySelector('.header-actions');
    if (!toggle || !headerActions) {
      return;
    }
    toggle.addEventListener('click', function () {
      var isOpen = headerActions.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  })();
</script>
<div class="search-modal" id="search-modal" aria-hidden="true" role="dialog">
  <div class="search-backdrop" data-search-close></div>
  <div class="search-panel" role="document">
    <div class="search-panel-row">
      <span class="search-panel-icon-wrap" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="search-panel-spinner">
          <path d="M21 12a9 9 0 1 1-6.219-8.56"></path>
        </svg>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="search-panel-icon">
          <circle cx="11" cy="11" r="8"></circle>
          <path d="m21 21-4.3-4.3"></path>
        </svg>
      </span>
      <form class="search-panel-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <input type="search" name="s" id="modal-search" placeholder="Search" autocomplete="off" class="search-panel-input">
      </form>
      <button class="search-panel-esc" type="button" data-search-close aria-label="Close search">Esc</button>
    </div>
  </div>
</div>

<script>
  (function () {
    var modal = document.getElementById('search-modal');
    var triggers = document.querySelectorAll('.header-search-trigger');
    var modifierLabels = document.querySelectorAll('.header-search-modifier');
    var input = document.getElementById('modal-search');
    var ignoreCloseUntil = 0;
    var platform = navigator.userAgentData && typeof navigator.userAgentData.platform === 'string' ? navigator.userAgentData.platform : navigator.platform;
    var isApplePlatform = /Mac|iPhone|iPad|iPod/i.test(platform || '');

    if (modifierLabels.length) {
      modifierLabels.forEach(function (label) {
        label.textContent = isApplePlatform ? 'Cmd' : 'Ctrl';
      });
    }

    function openModal() {
      if (!modal) {
        return;
      }
      modal.style.display = 'flex';
      modal.classList.add('is-open', 'is-opening');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('search-open');
      ignoreCloseUntil = Date.now() + 250;
      setTimeout(function () {
        modal.classList.remove('is-opening');
      }, 200);
      if (input) {
        input.focus();
      }
    }

    function closeModal() {
      if (!modal || !modal.classList.contains('is-open')) {
        return;
      }
      modal.classList.add('is-closing');
      setTimeout(function () {
        modal.classList.remove('is-open', 'is-closing');
        modal.setAttribute('aria-hidden', 'true');
        modal.style.display = 'none';
        document.body.classList.remove('search-open');
      }, 200);
    }

    if (triggers.length) {
      triggers.forEach(function (trigger) {
        trigger.addEventListener('click', function (event) {
          event.preventDefault();
          event.stopPropagation();
          openModal();
        });
      });
    }

    document.addEventListener('click', function (event) {
      // 如果点击的是评论相关链接，不要打开搜索框
      var target = event.target.closest('a');
      if (target && (target.classList.contains('comment-reply-link') || 
                     target.href && target.href.indexOf('#comment-') !== -1)) {
        return;
      }
    });

    document.addEventListener('keydown', function (event) {
      if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
        event.preventDefault();
        openModal();
      }
      if (event.key === 'Escape') {
        closeModal();
      }
    });

    if (modal) {
      modal.querySelectorAll('[data-search-close]').forEach(function (el) {
        el.addEventListener('click', function () {
          if (Date.now() < ignoreCloseUntil) {
            return;
          }
          closeModal();
        });
      });
    }
  })();
</script>
<script>
  (function() {
    var ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';

    document.addEventListener('click', function(e) {
      // Delete Comment
      if (e.target.classList.contains('delete-comment-btn')) {
        if (!confirm('确定要删除这条评论吗？')) return;
        
        var btn = e.target;
        var commentId = btn.getAttribute('data-id');
        var nonce = btn.getAttribute('data-nonce');
        var commentEl = btn.closest('li.comment'); // Assuming standard WP structure

        btn.disabled = true;
        btn.textContent = '删除中...';

        var formData = new FormData();
        formData.append('action', 'chickensoft_delete_comment');
        formData.append('comment_id', commentId);
        formData.append('nonce', nonce);

        fetch(ajaxUrl, {
          method: 'POST',
          body: formData
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
          if (data.success) {
            if (commentEl) {
              commentEl.style.opacity = '0.5';
              commentEl.innerHTML = '<div class="comment-body" style="padding: 20px; text-align: center; color: var(--color-fd-muted-foreground);">评论已删除</div>';
            } else {
              location.reload();
            }
          } else {
            alert('删除失败: ' + (data.data || '未知错误'));
            btn.disabled = false;
            btn.textContent = '删除';
          }
        })
        .catch(function(err) {
          alert('请求失败');
          btn.disabled = false;
          btn.textContent = '删除';
        });
      }

      // Edit Comment
      if (e.target.classList.contains('edit-comment-btn')) {
        var btn = e.target;
        var commentId = btn.getAttribute('data-id');
        var nonce = btn.getAttribute('data-nonce');
        
        // Store the comment element to update later
        window.currentEditingCommentBody = btn.closest('.comment-body');

        btn.disabled = true;
        btn.textContent = '加载中...';

        fetch(ajaxUrl + '?action=chickensoft_get_comment&comment_id=' + commentId + '&nonce=' + nonce)
        .then(function(res) { return res.json(); })
        .then(function(data) {
          btn.disabled = false;
          btn.textContent = '编辑';

          if (data.success) {
            var modal = document.getElementById('edit-comment-modal');
            var textarea = document.getElementById('edit-comment-textarea');
            var idInput = document.getElementById('edit-comment-id');
            var nonceInput = document.getElementById('edit-comment-nonce');

            textarea.value = data.data.content;
            idInput.value = commentId;
            nonceInput.value = nonce;

            document.body.classList.add('editing-active');
            textarea.focus();
          } else {
            alert('无法加载评论内容: ' + (data.data || '未知错误'));
          }
        })
        .catch(function(err) {
          btn.disabled = false;
          btn.textContent = '编辑';
          alert('请求失败');
        });
      }

      // Cancel Edit (Modal)
      if (e.target.id === 'cancel-edit-modal-btn' || e.target.closest('#cancel-edit-modal-btn')) {
        document.body.classList.remove('editing-active');
      }

      // Save Comment (Modal)
      if (e.target.id === 'save-edit-modal-btn' || e.target.closest('#save-edit-modal-btn')) {
        e.preventDefault();
        
        var btn = e.target.closest('button') || e.target;
        var modal = document.getElementById('edit-comment-modal');
        var textarea = document.getElementById('edit-comment-textarea');
        var idInput = document.getElementById('edit-comment-id');
        var nonceInput = document.getElementById('edit-comment-nonce');
        
        var content = textarea.value;
        var commentId = idInput.value;
        var nonce = nonceInput.value;

        btn.disabled = true;
        btn.textContent = '保存中...';

        var formData = new FormData();
        formData.append('action', 'chickensoft_save_comment');
        formData.append('comment_id', commentId);
        formData.append('nonce', nonce);
        formData.append('content', content);

        fetch(ajaxUrl, {
          method: 'POST',
          body: formData
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
          btn.disabled = false;
          btn.textContent = '保存修改';

          if (data.success) {
            document.body.classList.remove('editing-active');
            
            if (window.currentEditingCommentBody) {
               var contentEl = window.currentEditingCommentBody.querySelector('.comment-content');
               if (contentEl) {
                   contentEl.innerHTML = data.data.html;
               } else {
                   // Fallback: try to find where content is if structure is different
                   // Or reload if we can't find it
                   location.reload();
               }
            } else {
               location.reload();
            }
          } else {
            alert('保存失败: ' + (data.data || '未知错误'));
          }
        })
        .catch(function(err) {
          alert('请求失败');
          btn.disabled = false;
          btn.textContent = '保存修改';
        });
      }
    });
  })();
</script>
<script>
  (function() {
    var header = document.querySelector('.site-header');
    var lastScrollTop = 0;
    var headerHeight = header ? header.offsetHeight : 60;

    window.addEventListener('scroll', function() {
      if (!header) return;
      
      var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      
      if (scrollTop < 0) scrollTop = 0;

      // Always show header at top
      if (scrollTop < 10) {
        header.classList.remove('is-hidden');
        lastScrollTop = scrollTop;
        return;
      }

      if (Math.abs(lastScrollTop - scrollTop) <= 5) return;

      if (scrollTop > lastScrollTop && scrollTop > headerHeight) {
        // Scroll Down
        header.classList.add('is-hidden');
      } else {
        // Scroll Up
        header.classList.remove('is-hidden');
      }
      
      lastScrollTop = scrollTop;
    }, { passive: true });
  })();
</script>
<div id="edit-comment-modal">
  <h3 style="margin-top: 0; margin-bottom: 16px; font-size: 1.25rem; font-weight: 600;">编辑评论</h3>
  <textarea id="edit-comment-textarea" style="width: 100%; min-height: 120px; margin-bottom: 16px; padding: 12px; border-radius: 8px; border: 1px solid var(--color-fd-border); background: var(--color-fd-background); color: var(--color-fd-foreground); resize: vertical;"></textarea>
  <div class="edit-modal-actions">
    <button type="button" id="cancel-edit-modal-btn" class="edit-modal-btn cancel">取消</button>
    <button type="button" id="save-edit-modal-btn" class="edit-modal-btn save">保存修改</button>
  </div>
  <input type="hidden" id="edit-comment-id">
  <input type="hidden" id="edit-comment-nonce">
</div>

<script>
  (function() {
    const detailsElements = document.querySelectorAll('.sidebar-section');
    
    detailsElements.forEach(details => {
      const summary = details.querySelector('summary');
      if (!summary) return;
      
      const content = details.querySelector('ul');
      if (!content) return;

      // Set initial state
      if (details.hasAttribute('open')) {
        content.style.height = 'auto';
        content.style.opacity = '1';
      } else {
        content.style.height = '0px';
        content.style.opacity = '0';
        content.style.overflow = 'hidden';
      }

      summary.addEventListener('click', (e) => {
        e.preventDefault();
        
        const isOpen = details.hasAttribute('open');
        
        if (isOpen) {
          // Close animation
          const height = content.offsetHeight;
          content.style.height = height + 'px';
          
          // Force reflow
          content.offsetHeight;
          
          content.style.transition = 'height 0.25s ease-out, opacity 0.25s ease-out';
          content.style.height = '0px';
          content.style.opacity = '0';
          content.style.overflow = 'hidden';
          
          setTimeout(() => {
            details.removeAttribute('open');
            content.style.transition = '';
          }, 250);
        } else {
          // Open animation
          details.setAttribute('open', '');
          
          content.style.height = 'auto';
          const height = content.offsetHeight;
          
          content.style.height = '0px';
          content.style.opacity = '0';
          
          // Force reflow
          content.offsetHeight;
          
          content.style.transition = 'height 0.25s ease-out, opacity 0.25s ease-out';
          content.style.height = height + 'px';
          content.style.opacity = '1';
          
          setTimeout(() => {
            content.style.height = 'auto';
            content.style.overflow = 'visible';
            content.style.transition = '';
          }, 250);
        }
      });
    });
  })();

  window.initToc = function() {
    const tocLinks = document.querySelectorAll('.toc-list a');
    const sections = [];
    let isClickScrolling = false;
    let clickTimeout;
    
    // Cache the target elements for each TOC link
    tocLinks.forEach(link => {
      const href = link.getAttribute('href');
      const parts = href.split('#');
      if (parts.length < 2) return;
      const id = parts[1];
      const target = document.getElementById(id);
      if (target) {
        sections.push({ link, target });
        
        // Instant feedback on click
        link.addEventListener('click', function() {
          isClickScrolling = true;
          clearTimeout(clickTimeout);
          
          tocLinks.forEach(l => l.classList.remove('active'));
          this.classList.add('active');
          
          clickTimeout = setTimeout(() => {
            isClickScrolling = false;
          }, 800); // Resume tracking after scroll ends
        });
      }
    });

    function updateActiveToc() {
      if (isClickScrolling) return;

      const scrollHeight = document.documentElement.scrollHeight;
      const clientHeight = document.documentElement.clientHeight;
      const scrollPos = window.scrollY + 120; // Offset for header height
      
      let currentActive = null;

      // Check if we reached the bottom of the page
      if (window.scrollY + clientHeight >= scrollHeight - 20) {
        currentActive = sections[sections.length - 1];
      } else {
        for (const section of sections) {
          if (section.target.offsetTop <= scrollPos) {
            currentActive = section;
          } else {
            break;
          }
        }
      }

      tocLinks.forEach(link => link.classList.remove('active'));
      if (currentActive) {
        currentActive.link.classList.add('active');
      }
    }

    // Remove old listeners to prevent duplicates on Swup navigation
    window.removeEventListener('scroll', window._updateActiveToc);
    window.removeEventListener('resize', window._updateActiveToc);

    if (sections.length > 0) {
      window._updateActiveToc = updateActiveToc;
      window.addEventListener('scroll', window._updateActiveToc, { passive: true });
      window.addEventListener('resize', window._updateActiveToc);
      setTimeout(window._updateActiveToc, 150); 
    }
  };

  // Initialize on first load
  window.initToc();
</script>

<script>
window.initFriendModal = function() {
  var modal = document.getElementById('friend-modal');
  var openBtn = document.getElementById('open-friend-modal');
  if (!modal || !openBtn) return;

  var closeBtn = modal.querySelector('.friend-modal-close');
  var overlay = modal.querySelector('.friend-modal-overlay');
  var form = document.getElementById('friend-form');
  var closeTimer = null;

  function lockBodyScroll() {
    document.body.style.overflow = 'hidden';
  }

  function unlockBodyScroll() {
    if (!document.querySelector('.friend-modal.is-active, .friend-modal.is-closing')) {
      document.body.style.overflow = '';
    }
  }

  function openModal() {
    if (closeTimer) {
      clearTimeout(closeTimer);
      closeTimer = null;
    }
    modal.classList.remove('is-closing');
    modal.classList.add('is-active');
    lockBodyScroll();
  }

  function closeModal() {
    if (!modal.classList.contains('is-active') || modal.classList.contains('is-closing')) {
      return;
    }
    modal.classList.add('is-closing');
    closeTimer = window.setTimeout(function() {
      modal.classList.remove('is-closing', 'is-active');
      closeTimer = null;
      unlockBodyScroll();
    }, 220);
  }

  openBtn.addEventListener('click', openModal);
  closeBtn.addEventListener('click', closeModal);
  overlay.addEventListener('click', closeModal);

  // Close on Escape
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && modal.classList.contains('is-active')) {
      closeModal();
    }
  });

  if (form) {
    form.addEventListener('submit', function(e) {
      // 验证码检查
      var captcha = form.querySelector('input[name="cs_captcha"]');
      if (captcha && !captcha.value) {
        alert('请填写验证码');
        e.preventDefault();
        return;
      }

      var cfTurnstile = form.querySelector('[name="cf-turnstile-response"]');
      if (cfTurnstile && !cfTurnstile.value) {
        alert('请完成验证 (Turnstile)');
        e.preventDefault();
        return;
      }

      // 提示正在提交
      var submitBtn = form.querySelector('.submit-btn');
      var originalText = submitBtn.innerText;
      submitBtn.innerText = '提交中...';
      submitBtn.disabled = true;

      // 格式化评论内容
      var nameInput = document.getElementById('friend_name');
      var urlInput = document.getElementById('friend_url');
      var avatarInput = document.getElementById('friend_avatar');
      var descTextarea = document.getElementById('friend_desc');

      var name = nameInput ? nameInput.value : '';
      var url = urlInput ? urlInput.value : '';
      var avatar = avatarInput ? avatarInput.value : '';
      var desc = descTextarea ? descTextarea.value : '';
      
      // 拼接申请模板
      var formattedComment = "### 友链申请\n";
      formattedComment += "- **网站名称**: " + name + "\n";
      formattedComment += "- **网站链接**: " + url + "\n";
      formattedComment += "- **头像链接**: " + avatar + "\n";
      formattedComment += "- **网站简介**: " + (desc.trim() || "暂无简介");
      
      // 将拼接后的内容放入 comment 字段
      if (descTextarea) {
        descTextarea.value = formattedComment;
      }
    });
  }

  // Check for success param
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('friend_submit') === 'success') {
    var successModal = document.getElementById('friend-success-modal');
    if (successModal) {
      successModal.classList.add('is-active');
      lockBodyScroll();

      // Remove query param
      var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
      window.history.replaceState({path: newUrl}, '', newUrl);

      var successCloseTimer = null;
      function closeSuccessModal() {
        if (!successModal.classList.contains('is-active') || successModal.classList.contains('is-closing')) {
          return;
        }
        successModal.classList.add('is-closing');
        successCloseTimer = window.setTimeout(function() {
          successModal.classList.remove('is-closing', 'is-active');
          successCloseTimer = null;
          unlockBodyScroll();
        }, 220);
      }

      // Close handlers
      var successOverlay = successModal.querySelector('.friend-modal-overlay');
      if (successOverlay) {
        successOverlay.addEventListener('click', closeSuccessModal);
      }
      var closeSuccessBtn = document.getElementById('close-success-modal');
      if (closeSuccessBtn) {
        closeSuccessBtn.addEventListener('click', closeSuccessModal);
      }
    }
  }
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', window.initFriendModal);
} else {
  window.initFriendModal();
}
</script>

<script>
(function() {
  var lightbox = null;
  var image = null;

  function ensureLightbox() {
    if (lightbox) return;
    lightbox = document.createElement('div');
    lightbox.className = 'cs-lightbox';
    lightbox.innerHTML = '<button type="button" class="cs-lightbox__close" aria-label="关闭图片预览">&times;</button><img class="cs-lightbox__image" alt="图片预览">';
    document.body.appendChild(lightbox);
    image = lightbox.querySelector('.cs-lightbox__image');

    lightbox.addEventListener('click', function(event) {
      if (event.target === lightbox || event.target.classList.contains('cs-lightbox__close')) {
        closeLightbox();
      }
    });
  }

  function openLightbox(url, alt) {
    ensureLightbox();
    image.src = url;
    image.alt = alt || '图片预览';
    lightbox.classList.add('is-active');
    document.body.style.overflow = 'hidden';
  }

  function closeLightbox() {
    if (!lightbox) return;
    lightbox.classList.remove('is-active');
    image.src = '';
    document.body.style.overflow = '';
  }

  document.addEventListener('click', function(event) {
    var trigger = event.target.closest('[data-cs-lightbox="image"]');
    if (!trigger) return;
    event.preventDefault();
    var img = trigger.querySelector('img');
    openLightbox(trigger.href, img ? img.alt : '图片预览');
  });

  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && lightbox && lightbox.classList.contains('is-active')) {
      closeLightbox();
    }
  });
})();
</script>

</body>
</html>
