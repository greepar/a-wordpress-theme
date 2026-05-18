<?php get_header(); ?>

<div class="container">
  <div class="content-layout">
    <div>
      <h1 class="page-title">
        <?php 
        if (is_author()) {
          echo '作者: ' . get_the_author();
        } else {
          the_archive_title(); 
        }
        ?>
      </h1>

      <?php if (have_posts()) : ?>
        <?php
        $grid_class = 'post-grid post-grid-archive';
        if (is_author()) {
          $grid_class = 'post-grid';
        }
        if (!empty($wp_query) && $wp_query->post_count <= 2) {
          $grid_class .= ' post-grid-sparse';
        }
        ?>
        <div class="<?php echo esc_attr($grid_class); ?>">
          <?php while (have_posts()) : the_post(); ?>
            <?php if (is_author()) : ?>
              <article <?php post_class('post-card'); ?>>
                <a class="post-card-link" href="<?php the_permalink(); ?>">
                  <header class="post-card-header">
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
            <?php else : ?>
              <article <?php post_class('post-card post-card-horizontal'); ?>>
                <a class="post-card-link" href="<?php the_permalink(); ?>">
                  <?php if (has_post_thumbnail()) : ?>
                    <div class="post-card-image">
                      <span class="post-date-badge"><?php echo get_the_date('Y-m-d'); ?></span>
                      <?php the_post_thumbnail('large'); ?>
                    </div>
                  <?php endif; ?>
                  
                  <div class="post-card-content">
                    <header class="post-card-header">
                      <h2 class="post-card-title"><?php the_title(); ?></h2>
                      <p class="post-card-excerpt"><?php echo esc_html(wp_trim_words(get_the_content(), 120)); ?></p>
                    </header>

                    <footer class="post-card-footer">
                      <span>Read More -></span>
                    </footer>
                  </div>
                </a>
              </article>
            <?php endif; ?>
          <?php endwhile; ?>
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
