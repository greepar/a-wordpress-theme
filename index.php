<?php get_header(); ?>

<div class="container">
  <div class="content-layout">
    <div class="main-content-area">
      <?php if (is_search()) : ?>
        <h1 class="page-title">搜索结果: <?php echo get_search_query(); ?></h1>
      <?php else : ?>
        <h1 class="page-title">首页</h1>
      <?php endif; ?>

      <?php if (have_posts()) : ?>
        <?php
        $grid_class = 'post-grid';
        if (is_archive() && !is_category() && !is_author()) {
          $grid_class .= ' post-grid-archive';
        }
        if (!empty($wp_query) && $wp_query->post_count <= 2) {
          $grid_class .= ' post-grid-sparse';
        }
        ?>
        <div class="<?php echo esc_attr($grid_class); ?>">
          <?php $post_idx = 0; while (have_posts()) : the_post(); ?>
            <article <?php post_class('post-card' . ($post_idx === 0 && is_home() && !is_paged() ? ' latest-post-highlight' : '')); ?>>
              <a class="post-card-link" href="<?php the_permalink(); ?>">
                <header class="post-card-header">
                  <?php if ($post_idx === 0 && is_home() && !is_paged()) : ?>
                    <div class="latest-post-meta">
                      <span class="latest-post-badge">最新发布</span>
                      <span class="latest-post-date">[<?php echo get_the_date(); ?>]</span>
                    </div>
                  <?php endif; ?>
                  <h2 class="post-card-title"><?php the_title(); ?></h2>
                  <p class="post-card-excerpt"><?php echo esc_html(wp_trim_words(get_the_content(), 120)); ?></p>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                  <div class="post-card-image">
                    <?php the_post_thumbnail('large'); ?>
                  </div>
                <?php endif; ?>

                <footer class="post-card-footer">
                  <span>Read More -></span>
                </footer>
              </a>
            </article>
          <?php $post_idx++; endwhile; ?>
        </div>

        <div class="post-navigation">
          <?php the_posts_pagination(); ?>
        </div>
      <?php else : ?>
        <p>No posts found.</p>
      <?php endif; ?>
    </div>

    <?php get_sidebar(); ?>
  </div>
</div>

<?php get_footer(); ?>
