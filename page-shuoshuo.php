<?php
/*
Template Name: 说说
*/
get_header();

$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$shuoshuo_query = new WP_Query(array(
    'post_type'      => 'shuoshuo',
    'posts_per_page' => 10,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
));
?>

<div class="container">
  <section class="shuoshuo-hero">
    <div class="shuoshuo-hero-content">
      <p class="shuoshuo-kicker">记录生活中的点点滴滴</p>
      <h1 class="page-title"><?php the_title(); ?></h1>
    </div>
    <div class="shuoshuo-hero-action">
      <button type="button" class="shuoshuo-publish-btn" onclick="openShuoshuoPublishModal()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>发布说说</span>
      </button>
    </div>
  </section>

  <?php if ($shuoshuo_query->have_posts()) : ?>
    <div class="shuoshuo-timeline">
      <?php while ($shuoshuo_query->have_posts()) : $shuoshuo_query->the_post(); ?>
        <article class="shuoshuo-card" id="shuoshuo-<?php the_ID(); ?>">
          <div class="shuoshuo-card-header">
            <div class="shuoshuo-card-avatar">
              <?php echo get_avatar(get_the_author_meta('ID'), 48); ?>
            </div>
            <div class="shuoshuo-card-meta">
              <span class="shuoshuo-card-author"><?php the_author(); ?></span>
              <time class="shuoshuo-card-time" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                <?php echo esc_html(chickensoft_shuoshuo_relative_time(get_the_time('U'))); ?>
              </time>
            </div>
          </div>

          <div class="shuoshuo-card-body">
            <div class="shuoshuo-card-content prose">
              <?php the_content(); ?>
            </div>

            <?php if (has_post_thumbnail()) : ?>
              <div class="shuoshuo-card-image">
                <?php the_post_thumbnail('medium_large'); ?>
              </div>
            <?php endif; ?>
          </div>

          <?php if (comments_open() || get_comments_number()) : ?>
            <div class="shuoshuo-card-footer">
              <button type="button" class="shuoshuo-comment-btn" data-post-id="<?php the_ID(); ?>" data-comment-form-url="<?php echo esc_url(trailingslashit(get_permalink()) . 'comment-form/'); ?>" data-comment-url="<?php echo esc_url(trailingslashit(get_permalink()) . 'comment/'); ?>" onclick="openShuoshuoCommentModal(this)">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                <span>
                  <?php
                  $count = get_comments_number();
                  echo $count > 0 ? sprintf('%s 条评论', number_format_i18n($count)) : '评论';
                  ?>
                </span>
              </button>

              <?php if ($count > 0) : ?>
                <button type="button" class="shuoshuo-toggle-comments-btn" data-post-id="<?php the_ID(); ?>" data-comment-url="<?php echo esc_url(trailingslashit(get_permalink()) . 'comment/'); ?>" onclick="toggleShuoshuoComments(this)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                  <span>展开评论</span>
                </button>
              <?php endif; ?>
            </div>

            <div class="shuoshuo-card-comments" id="shuoshuo-comments-<?php the_ID(); ?>" style="display:none;">
              <div class="shuoshuo-comments-loading">
                <p>正在加载评论...</p>
              </div>
            </div>
          <?php endif; ?>
        </article>
      <?php endwhile; ?>
    </div>

    <?php if ($shuoshuo_query->max_num_pages > 1) : ?>
      <div class="shuoshuo-pagination">
        <?php
        echo paginate_links(array(
            'total'   => $shuoshuo_query->max_num_pages,
            'current' => $paged,
            'prev_text' => '&laquo; 上一页',
            'next_text' => '下一页 &raquo;',
        ));
        ?>
      </div>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

  <?php else : ?>
    <div class="shuoshuo-empty">
      <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
      <p>还没有发表说说，敬请期待~</p>
    </div>
  <?php endif; ?>
</div>

<!-- Shuoshuo Comment Modal -->
<div id="shuoshuo-comment-modal">
    <div class="comment-modal-overlay" onclick="closeShuoshuoCommentModal()"></div>
    <div class="comment-modal-container">
        <div class="comment-modal-header">
            <span>发表评论</span>
            <button type="button" class="comment-modal-close" onclick="closeShuoshuoCommentModal()">&times;</button>
        </div>
        <iframe id="shuoshuo-comment-iframe" class="comment-modal-iframe"></iframe>
    </div>
</div>

<!-- Shuoshuo Publish Modal -->
<div id="shuoshuo-publish-modal">
    <div class="comment-modal-overlay" onclick="closeShuoshuoPublishModal()"></div>
    <div class="comment-modal-container">
        <div class="comment-modal-header">
            <span>发布说说</span>
            <button type="button" class="comment-modal-close" onclick="closeShuoshuoPublishModal()">&times;</button>
        </div>
        <iframe id="shuoshuo-publish-iframe" class="comment-modal-iframe"></iframe>
    </div>
</div>

<script>
  window.currentUserCanManageComments = <?php echo current_user_can('moderate_comments') ? 'true' : 'false'; ?>;

  function openShuoshuoCommentModal(btn) {
    var modal = document.getElementById('shuoshuo-comment-modal');
    var iframe = document.getElementById('shuoshuo-comment-iframe');
    var formUrl = btn.getAttribute('data-comment-form-url');
    formUrl += (formUrl.includes('?') ? '&' : '?') + 'nocache=' + Date.now();
    iframe.src = formUrl;
    modal.classList.remove('is-closing');
    modal.classList.add('is-active');
  }

  function closeShuoshuoCommentModal() {
    var modal = document.getElementById('shuoshuo-comment-modal');
    var iframe = document.getElementById('shuoshuo-comment-iframe');
    if (!modal || !modal.classList.contains('is-active')) return;
    modal.classList.add('is-closing');
    setTimeout(function() {
      modal.classList.remove('is-active', 'is-closing');
      iframe.src = '';
    }, 220);
  }

  function openShuoshuoPublishModal() {
    var modal = document.getElementById('shuoshuo-publish-modal');
    var iframe = document.getElementById('shuoshuo-publish-iframe');
    var publishUrl = '<?php
      $shuoshuo_slug = sanitize_title(get_theme_mod("shuoshuo_slug", "moments"));
      if (empty($shuoshuo_slug)) $shuoshuo_slug = "moments";
      echo esc_js(home_url("/" . $shuoshuo_slug . "/publish-form/"));
    ?>';
    publishUrl += (publishUrl.includes('?') ? '&' : '?') + 'nocache=' + Date.now();
    iframe.src = publishUrl;
    modal.classList.remove('is-closing');
    modal.classList.add('is-active');
  }

  function closeShuoshuoPublishModal() {
    var modal = document.getElementById('shuoshuo-publish-modal');
    var iframe = document.getElementById('shuoshuo-publish-iframe');
    if (!modal || !modal.classList.contains('is-active')) return;
    modal.classList.add('is-closing');
    setTimeout(function() {
      modal.classList.remove('is-active', 'is-closing');
      iframe.src = '';
    }, 220);
  }

  window.addEventListener('message', function(event) {
    if (!event.data || !event.data.type) return;
    if (event.data.type === 'comment_success') {
      closeShuoshuoCommentModal();
      setTimeout(function() { location.reload(); }, 500);
    }
    if (event.data.type === 'shuoshuo_publish_success') {
      closeShuoshuoPublishModal();
      setTimeout(function() { location.reload(); }, 500);
    }
    if (event.data.type === 'iframe_height') {
      var publishModal = document.getElementById('shuoshuo-publish-modal');
      var commentModal = document.getElementById('shuoshuo-comment-modal');
      if (publishModal && publishModal.classList.contains('is-active')) {
        var pf = document.getElementById('shuoshuo-publish-iframe');
        if (pf) pf.style.height = event.data.height + 'px';
      } else if (commentModal && commentModal.classList.contains('is-active')) {
        var cf = document.getElementById('shuoshuo-comment-iframe');
        if (cf) cf.style.height = event.data.height + 'px';
      }
    }
  });

  function toggleShuoshuoComments(btn) {
    var postId = btn.getAttribute('data-post-id');
    var container = document.getElementById('shuoshuo-comments-' + postId);
    var spanEl = btn.querySelector('span');
    if (container.style.display === 'none') {
      container.style.display = 'block';
      spanEl.textContent = '收起评论';
      btn.classList.add('is-expanded');
      if (container.querySelector('.shuoshuo-comments-loading')) {
        var commentUrl = btn.getAttribute('data-comment-url');
        fetch(commentUrl)
          .then(function(res) { return res.text(); })
          .then(function(html) { container.innerHTML = html; })
          .catch(function() {
            container.innerHTML = '<p style="text-align:center;padding:12px;color:var(--color-fd-muted-foreground);">加载评论失败</p>';
          });
      }
    } else {
      container.style.display = 'none';
      spanEl.textContent = '展开评论';
      btn.classList.remove('is-expanded');
    }
  }

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      var pm = document.getElementById('shuoshuo-publish-modal');
      var cm = document.getElementById('shuoshuo-comment-modal');
      if (pm && pm.classList.contains('is-active')) closeShuoshuoPublishModal();
      else if (cm && cm.classList.contains('is-active')) closeShuoshuoCommentModal();
    }
  });
</script>

<?php get_footer(); ?>
