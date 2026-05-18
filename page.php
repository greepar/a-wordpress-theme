<?php get_header(); ?>

<div class="container">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <article <?php post_class(); ?>>
      <h1 class="page-title"><?php the_title(); ?></h1>
      <div class="prose">
        <?php the_content(); ?>
      </div>
    </article>
  <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
