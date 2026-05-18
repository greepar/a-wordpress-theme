<?php
/*
Template Name: Friend Links
*/
get_header();
?>

<div class="container">
  <section class="friend-hero">
    <div class="friend-hero-header">
      <div class="friend-hero-content">
        <p class="friend-kicker">认识一些很棒的朋友</p>
        <h1 class="page-title"><?php the_title(); ?></h1>
      </div>
      <div class="friend-hero-action">
        <button type="button" class="friend-apply-btn" id="open-friend-modal">
          <svg class="icon-plus" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
          </svg>
          <span>申请友链</span>
        </button>
      </div>
    </div>
    <p class="friend-subtitle">欢迎互换友链，点击头像或名字就能访问对方站点。</p>
  </section>

  <!-- Friend Link Modal -->
  <div id="friend-modal" class="friend-modal">
    <div class="friend-modal-overlay"></div>
    <div class="friend-modal-container">
      <div class="friend-modal-header">
        <h3>申请友链</h3>
        <button type="button" class="friend-modal-close">&times;</button>
      </div>
      <form id="friend-form" method="post" action="<?php echo esc_url(site_url('/wp-comments-post.php')); ?>">
        <div class="friend-modal-body">
          <div class="form-group">
            <label for="friend_name">网站名称 *</label>
            <input type="text" id="friend_name" name="author" required placeholder="如：格瑞普的小站">
          </div>
          <div class="form-group">
            <label for="friend_email">电子邮箱 * (不公开，仅用于核实)</label>
            <input type="email" id="friend_email" name="email" required placeholder="example@mail.com">
          </div>
          <div class="form-group">
            <label for="friend_url">网站链接 *</label>
            <input type="url" id="friend_url" name="url" required placeholder="https://example.com">
          </div>
          <div class="form-group">
            <label for="friend_avatar">头像链接 *</label>
            <input type="url" id="friend_avatar" name="friend_avatar" required placeholder="https://example.com/avatar.png">
          </div>
          <div class="form-group">
            <label for="friend_desc">简介</label>
            <textarea id="friend_desc" name="comment" rows="3" placeholder="写一点关于你站点的介绍吧..."></textarea>
          </div>
          
          <?php 
          // 渲染验证码（如果有）
          do_action('comment_form_after_fields'); 
          ?>

          <input type="hidden" name="comment_post_ID" value="<?php echo get_the_ID(); ?>" id="comment_post_ID">
          <input type="hidden" name="comment_parent" id="comment_parent" value="0">
        </div>
        <div class="friend-modal-footer">
          <button type="submit" class="submit-btn">立即申请</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Success Modal -->
  <div id="friend-success-modal" class="friend-modal">
    <div class="friend-modal-overlay"></div>
    <div class="friend-modal-container" style="max-width: 400px; text-align: center;">
      <div class="friend-modal-body" style="padding: 40px 24px;">
         <div style="width: 64px; height: 64px; background: #e0f2fe; border-radius: 50%; color: var(--color-fd-primary); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
         </div>
         <h3 style="margin: 0 0 12px; font-size: 1.25rem;">申请成功</h3>
         <p style="margin: 0; color: var(--color-fd-muted-foreground);">申请信息已发送至您的邮箱，<br>请留意查收。</p>
      </div>
      <div class="friend-modal-footer" style="text-align: center;">
         <button type="button" class="submit-btn" id="close-success-modal">确定</button>
      </div>
    </div>
  </div>

  <div class="friend-sections">
    <?php
    while (have_posts()) : the_post();
        the_content();
    endwhile;
    ?>
  </div>
</div>

<?php get_footer(); ?>
