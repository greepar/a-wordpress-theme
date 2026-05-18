<?php get_header(); ?>

<?php
$not_found_image = get_theme_mod('not_found_image', '');
?>

<div class="container">
  <section class="not-found-page">
    <?php if (!empty($not_found_image)) : ?>
      <img
        id="not-found-image"
        class="not-found-image"
        src="<?php echo esc_url($not_found_image); ?>"
        alt="<?php esc_attr_e('404 Not Found', 'chickensoft-blog'); ?>"
        loading="eager"
      />
    <?php endif; ?>

    <h1 class="not-found-title">404</h1>
    <p class="not-found-text">你访问的页面不存在，可能已被移动或删除。</p>
  </section>
</div>

<script>
  (function() {
    var body = document.body;
    if (!body) return;

    var image = document.getElementById('not-found-image');
    if (!image) return;

    body.classList.add('not-found-loading');

    function reveal() {
      body.classList.remove('not-found-loading');
      body.classList.add('not-found-ready');
    }

    if (image.complete) {
      requestAnimationFrame(function() {
        requestAnimationFrame(reveal);
      });
      return;
    }

    image.addEventListener('load', reveal, { once: true });
    image.addEventListener('error', reveal, { once: true });
  })();
</script>

<?php get_footer(); ?>
