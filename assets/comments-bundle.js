(function() {
    function updateCommentsTitleFromFragment(container) {
        const titleEl = document.querySelector('#comments > .comments-header > h2');
        if (!titleEl || !container) return;
        const count = container.querySelectorAll('li.comment').length;
        titleEl.textContent = count + ' 条评论';
    }

    // 1. Comments Loading & Fragment Logic
    window.initComments = function(isRefresh = false) {
        const container = document.querySelector('.comments-fragment');
        if (!container) return;
        const url = container.getAttribute('data-comments-url');
        if (!url) return;
        
        const load = () => {
            if (!isRefresh) container.innerHTML = '<p style="text-align:center;padding:20px;">正在加载评论...</p>';
            fetch(url, { cache: 'no-cache' })
                .then(r => r.text())
                .then(html => {
                    container.innerHTML = html;
                    updateCommentsTitleFromFragment(container);
                    
                    // Intercept reply links to open modal
                    container.querySelectorAll('.comment-reply-link').forEach(link => {
                        link.addEventListener('click', function(e) {
                            e.preventDefault();
                            const replyId = this.getAttribute('data-commentid');
                            window.openCommentModal(replyId);
                        });
                    });
                }).catch(() => { if (!isRefresh) container.innerHTML = '<p style="text-align:center;padding:20px;">评论加载失败</p>'; });
        };
        load();
    };

    // 3. Modal Logic
    window.openCommentModal = function(replyToId = null) {
        const modal = document.getElementById('comment-modal');
        const iframe = document.getElementById('comment-iframe');
        const title = modal.querySelector('span');
        if (!modal || !iframe) return;
        if (modal._closeTimer) {
            clearTimeout(modal._closeTimer);
            modal._closeTimer = null;
        }
        
        let src = iframe.getAttribute('data-src');
        if (replyToId) {
            src += (src.includes('?') ? '&' : '?') + 'replytocom=' + replyToId;
            if (title) title.innerText = '回复评论';
        } else {
            if (title) title.innerText = '发表评论';
        }
        
        // Bust cache to prevent Cloudflare from serving an outdated or blank iframe
        src += (src.includes('?') ? '&' : '?') + 'nocache=' + new Date().getTime();
        
        iframe.src = src;
        modal.classList.remove('is-closing');
        modal.classList.add('is-active');
        // document.body.style.overflow = 'hidden'; // Removed to allow scrolling background article
    };

    window.closeCommentModal = function() {
        const modal = document.getElementById('comment-modal');
        const iframe = document.getElementById('comment-iframe');
        if (!modal || modal.classList.contains('is-closing') || !modal.classList.contains('is-active')) return;

        modal.classList.add('is-closing');
        modal._closeTimer = window.setTimeout(() => {
            modal.classList.remove('is-closing', 'is-active');
            modal._closeTimer = null;
            if (iframe) iframe.src = ''; // Clear iframe to stop media/scripts
            document.body.classList.remove('replying-active'); // Cleanup legacy class
        }, 220);
    };

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.closeCommentModal();
        }
    });

    // 4. Listen for messages from iframe
    window.addEventListener('message', function(event) {
        if (event.data && event.data.type === 'comment_success') {
            window.closeCommentModal();
            window.initComments(true); // Refresh comments
        } else if (event.data && event.data.type === 'comment_error') {
            console.error('Comment error:', event.data.msg);
        } else if (event.data && event.data.type === 'iframe_height') {
            const iframe = document.getElementById('comment-iframe');
            if (iframe) {
                iframe.style.height = event.data.height + 'px';
            }
        }
    });

    // 5. Captcha refresh logic (works for main comment form + friend modal + iframe form)
    document.addEventListener('click', function(e) {
        const refreshTrigger = e.target.closest('.cs-captcha-refresh, .cs-captcha-image');
        if (!refreshTrigger) return;

        const wrap = refreshTrigger.closest('.comment-form-captcha');
        if (!wrap) return;

        const refreshUrl = refreshTrigger.getAttribute('data-refresh-url');
        if (!refreshUrl) return;

        e.preventDefault();

        if (refreshTrigger.tagName === 'BUTTON') {
            refreshTrigger.disabled = true;
        }
        const image = wrap.querySelector('.cs-captcha-image');
        const oldOpacity = image ? image.style.opacity : '';
        if (image) {
            image.style.opacity = '0.6';
        }

        fetch(refreshUrl, {
            method: 'GET',
            cache: 'no-store',
            credentials: 'same-origin'
        })
            .then((r) => r.json())
            .then((payload) => {
                if (!payload || !payload.success || !payload.data) {
                    throw new Error('Invalid captcha payload');
                }

                const keyInput = wrap.querySelector('input[name="cs_captcha_key"]');
                const image = wrap.querySelector('.cs-captcha-image');
                const textInput = wrap.querySelector('input[name="cs_captcha"]');

                if (keyInput && payload.data.key) {
                    keyInput.value = payload.data.key;
                }
                if (image && payload.data.image_url) {
                    image.src = payload.data.image_url + '&_cs=' + Date.now();
                }
                if (textInput) {
                    textInput.value = '';
                    textInput.focus();
                }
            })
            .catch(() => {
                const image = wrap.querySelector('.cs-captcha-image');
                if (image && image.src) {
                    const cleanSrc = image.src.replace(/([?&])_cs=\d+/, '').replace(/[?&]$/, '');
                    image.src = cleanSrc + (cleanSrc.includes('?') ? '&' : '?') + '_cs=' + Date.now();
                }
            })
            .finally(() => {
                if (refreshTrigger.tagName === 'BUTTON') {
                    refreshTrigger.disabled = false;
                }
                if (image) {
                    image.style.opacity = oldOpacity;
                }
            });
    });

    // Initial load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => window.initComments());
    } else {
        window.initComments();
    }
})();
