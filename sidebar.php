<aside class="sidebar" role="complementary">
  <?php if (is_singular('post')) : ?>
    <?php
    $toc_data = chickensoft_blog_generate_heading_ids(get_post_field('post_content', get_the_ID()), true);
    $headings = $toc_data['headings'];
    ?>
    <?php if (!empty($headings)) : ?>
      <div class="sidebar-section sidebar-toc">
        <h2>文章目录</h2>
        <ul class="toc-list">
          <?php foreach ($headings as $heading) : ?>
            <li class="toc-item toc-level-<?php echo (int) $heading['level']; ?>">
              <a href="<?php echo esc_url(get_permalink() . '#' . $heading['id']); ?>">
                <?php echo esc_html($heading['text']); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <details class="sidebar-section sidebar-recent-posts" <?php echo is_singular('post') ? '' : 'open'; ?>>
    <summary><h2>最新文章</h2></summary>
    <ul>
      <?php
      $recent_posts = wp_get_recent_posts(
          array(
              'numberposts' => 6,
              'post_status' => 'publish',
          )
      );
      foreach ($recent_posts as $post) :
      ?>
        <li><a href="<?php echo esc_url(get_permalink($post['ID'])); ?>"><?php echo esc_html($post['post_title']); ?></a></li>
      <?php endforeach; wp_reset_query(); ?>
    </ul>
  </details>
</aside>
