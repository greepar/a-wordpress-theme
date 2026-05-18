<?php
/*
 * Single Shuoshuo (说说) Template
 * Displays a single shuoshuo post with its comments.
 */
get_header();
?>

<div class="container">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <div class="shuoshuo-single-wrap">
      <a href="<?php
        // Link back to the shuoshuo listing page
        $page = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'page-shuoshuo.php', 'number' => 1));
        echo $page ? esc_url(get_permalink($page[0]->ID)) : esc_url(home_url('/'));
      ?>" class="shuoshuo-back-link">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
        返回说说列表
      </a>

      <article class="shuoshuo-single-article">
        <div class="shuoshuo-single-header">
          <div class="shuoshuo-single-avatar">
            <?php echo get_avatar(get_the_author_meta('ID'), 64); ?>
          </div>
          <div class="shuoshuo-single-meta">
            <span class="shuoshuo-single-author"><?php the_author(); ?></span>
            <time class="shuoshuo-single-time" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
              <?php echo esc_html(chickensoft_shuoshuo_relative_time(get_the_time('U'))); ?>
            </time>
          </div>
        </div>

        <div class="shuoshuo-single-body">
          <div class="shuoshuo-single-content prose">
            <?php the_content(); ?>
          </div>

          <?php if (has_post_thumbnail()) : ?>
            <div class="shuoshuo-single-image">
              <?php the_post_thumbnail('large'); ?>
            </div>
          <?php endif; ?>
        </div>
      </article>

      <?php if (comments_open() || get_comments_number()) : ?>
        <?php comments_template(); ?>
      <?php endif; ?>
    </div>
  <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
