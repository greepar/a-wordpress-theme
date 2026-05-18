<?php
if (post_password_required()) {
    return;
}
?>

<section class="comments-area" id="comments">
  <div class="comments-header">
    <h2 class="comments-title">
      <?php
      $comments_number = get_comments_number();
      if ($comments_number === 0) {
          echo '0 条评论';
      } else {
          printf(
              _n('1 条评论', '%s 条评论', $comments_number, 'chickensoft-blog'),
              number_format_i18n($comments_number)
          );
      }
      ?>
    </h2>
    <?php if (comments_open()) : ?>
      <button id="add-new-comment" class="add-comment-button" type="button" onclick="openCommentModal()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        发表评论
      </button>
    <?php endif; ?>
  </div>

  <div id="comments-container" class="comments-fragment" data-comments-url="<?php echo esc_url(trailingslashit(get_permalink()) . 'comment/'); ?>">
    <p style="text-align:center;padding:20px;">正在加载评论...</p>
  </div>

  <?php
  if (!comments_open() && get_comments_number()) :
      echo '<p class="no-comments">评论已关闭。</p>';
  endif;
  ?>
</section>

<!-- Comment Modal -->
<div id="comment-modal">
    <div class="comment-modal-overlay" onclick="closeCommentModal()"></div>
    <div class="comment-modal-container">
        <div class="comment-modal-header">
            <span>发表评论</span>
            <button type="button" class="comment-modal-close" onclick="closeCommentModal()">&times;</button>
        </div>
        <iframe id="comment-iframe" data-src="<?php echo esc_url(trailingslashit(get_permalink()) . 'comment-form/'); ?>" class="comment-modal-iframe"></iframe>
    </div>
</div>

<script>
    window.currentUserCanManageComments = <?php echo current_user_can('moderate_comments') ? 'true' : 'false'; ?>;
</script>
