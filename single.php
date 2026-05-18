<?php get_header(); ?>

<div class="container">
  <div class="content-layout">
    <div>
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article <?php post_class(); ?>>
          <h1 class="page-title"><?php the_title(); ?></h1>
          <p class="post-date"><?php echo esc_html(get_the_date()); ?></p>

          <?php if (has_excerpt()) : ?>
            <p class="post-meta"><?php echo esc_html(get_the_excerpt()); ?></p>
          <?php endif; ?>

          <div class="author-box">
            <a class="author-info" href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
              <?php echo get_avatar(get_the_author_meta('ID'), 96); ?>
              <div>
                <p class="author-name"><?php the_author(); ?></p>
                <?php if (get_the_author_meta('description')) : ?>
                  <p class="author-bio"><?php echo esc_html(get_the_author_meta('description')); ?></p>
                <?php else : ?>
                  <p class="author-bio">分享工具、开发与思考。</p>
                <?php endif; ?>
              </div>
            </a>
          </div>

          <?php 
          $featured_bg_enabled = get_post_meta(get_the_ID(), '_chickensoft_featured_bg_enabled', true) === 'yes';
          if (has_post_thumbnail() && !$featured_bg_enabled) : ?>
            <div class="post-hero">
              <?php the_post_thumbnail('large'); ?>
            </div>
          <?php endif; ?>

          <div class="prose">
            <?php the_content(); ?>
          </div>

          <?php if (comments_open() || get_comments_number()) : ?>
            <?php comments_template(); ?>
          <?php endif; ?>
        </article>
      <?php endwhile; endif; ?>
    </div>

    <?php get_sidebar(); ?>
  </div>
</div>

<?php get_footer(); ?>
