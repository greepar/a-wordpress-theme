<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="preload" href="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/fonts/catamaran/catamaran-latin-var.woff2" as="font" type="font/woff2" crossorigin="anonymous">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="page-progress" id="page-progress" aria-hidden="true"></div>

<?php
$header_icon_url = get_theme_mod('header_icon_url');
$header_icon_hover_url = get_theme_mod('header_icon_hover_url');
$github_url = get_theme_mod('github_url');
$github_icon_url = get_theme_mod('github_icon_url');
$bilibili_url = get_theme_mod('bilibili_url');
$bilibili_icon_url = get_theme_mod('bilibili_icon_url');
?>

<header class="site-header">
  <div class="site-header-inner">
    <a class="site-brand" href="<?php echo esc_url(home_url('/')); ?>">
      <div class="site-logo-div" role="img" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>"></div>
      <span class="site-title"><?php bloginfo('name'); ?></span>
    </a>

    <div class="header-actions">
      <nav class="primary-nav" id="primary-nav" aria-label="Primary">
        <?php
        wp_nav_menu(
            array(
                'theme_location' => 'primary',
                'container' => false,
                'menu_class' => '',
                'fallback_cb' => 'wp_page_menu',
            )
        );
        ?>
        <div class="nav-mobile-utility">
          <button class="header-search header-search-trigger" type="button" aria-haspopup="dialog" aria-controls="search-modal">
            <span class="header-search-icon" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.3-4.3"></path>
              </svg>
            </span>
            <span class="header-search-text">Search</span>
            <span class="header-search-kbd" aria-hidden="true">
              <kbd class="header-search-modifier">Ctrl</kbd>
              <kbd>K</kbd>
            </span>
          </button>

          <?php if ($github_url) : ?>
            <a class="header-icon-link" href="<?php echo esc_url($github_url); ?>" target="_blank" rel="noreferrer noopener" aria-label="GitHub">
              <?php if ($github_icon_url) : ?>
                <img src="<?php echo esc_url($github_icon_url); ?>" alt="">
              <?php else : ?>
                <svg role="img" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"></path>
                </svg>
              <?php endif; ?>
            </a>
          <?php endif; ?>

          <?php if ($bilibili_url) : ?>
            <a class="header-icon-link" href="<?php echo esc_url($bilibili_url); ?>" target="_blank" rel="noreferrer noopener" aria-label="Bilibili">
              <?php if ($bilibili_icon_url) : ?>
                <img src="<?php echo esc_url($bilibili_icon_url); ?>" alt="">
              <?php else : ?>
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="zhuzhan-icon" aria-hidden="true">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M3.73252 2.67094C3.33229 2.28484 3.33229 1.64373 3.73252 1.25764C4.11291 0.890684 4.71552 0.890684 5.09591 1.25764L7.21723 3.30403C7.27749 3.36218 7.32869 3.4261 7.37081 3.49407H10.5789C10.6211 3.4261 10.6723 3.36218 10.7325 3.30403L12.8538 1.25764C13.2342 0.890684 13.8368 0.890684 14.2172 1.25764C14.6175 1.64373 14.6175 2.28484 14.2172 2.67094L13.364 3.49407H14C16.2091 3.49407 18 5.28493 18 7.49407V12.9996C18 15.2087 16.2091 16.9996 14 16.9996H4C1.79086 16.9996 0 15.2087 0 12.9996V7.49406C0 5.28492 1.79086 3.49407 4 3.49407H4.58579L3.73252 2.67094ZM4 5.42343C2.89543 5.42343 2 6.31886 2 7.42343V13.0702C2 14.1748 2.89543 15.0702 4 15.0702H14C15.1046 15.0702 16 14.1748 16 13.0702V7.42343C16 6.31886 15.1046 5.42343 14 5.42343H4ZM5 9.31747C5 8.76519 5.44772 8.31747 6 8.31747C6.55228 8.31747 7 8.76519 7 9.31747V10.2115C7 10.7638 6.55228 11.2115 6 11.2115C5.44772 11.2115 5 10.7638 5 10.2115V9.31747ZM12 8.31747C11.4477 8.31747 11 8.76519 11 9.31747V10.2115C11 10.7638 11.4477 11.2115 12 11.2115C12.5523 11.2115 13 10.7638 13 10.2115V9.31747C13 8.76519 12.5523 8.31747 12 8.31747Z" fill="currentColor"></path>
                </svg>
              <?php endif; ?>
            </a>
          <?php endif; ?>
        </div>
      </nav>

      <button class="header-menu-toggle" type="button" aria-expanded="false" aria-controls="primary-nav" aria-label="Toggle menu">
        <span aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </span>
      </button>

      <button class="header-search header-search-trigger desktop-only" type="button" aria-haspopup="dialog" aria-controls="search-modal">
        <span class="header-search-icon" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.3-4.3"></path>
          </svg>
        </span>
        <span class="header-search-text">Search</span>
        <span class="header-search-kbd" aria-hidden="true">
          <kbd class="header-search-modifier">Ctrl</kbd>
          <kbd>K</kbd>
        </span>
      </button>

      <?php if ($github_url) : ?>
        <a class="header-icon-link desktop-only" href="<?php echo esc_url($github_url); ?>" target="_blank" rel="noreferrer noopener" aria-label="GitHub">
          <?php if ($github_icon_url) : ?>
            <img src="<?php echo esc_url($github_icon_url); ?>" alt="">
          <?php else : ?>
            <svg role="img" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"></path>
            </svg>
          <?php endif; ?>
        </a>
      <?php endif; ?>

      <?php if ($bilibili_url) : ?>
        <a class="header-icon-link desktop-only" href="<?php echo esc_url($bilibili_url); ?>" target="_blank" rel="noreferrer noopener" aria-label="Bilibili">
          <?php if ($bilibili_icon_url) : ?>
            <img src="<?php echo esc_url($bilibili_icon_url); ?>" alt="">
          <?php else : ?>
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="zhuzhan-icon" aria-hidden="true">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M3.73252 2.67094C3.33229 2.28484 3.33229 1.64373 3.73252 1.25764C4.11291 0.890684 4.71552 0.890684 5.09591 1.25764L7.21723 3.30403C7.27749 3.36218 7.32869 3.4261 7.37081 3.49407H10.5789C10.6211 3.4261 10.6723 3.36218 10.7325 3.30403L12.8538 1.25764C13.2342 0.890684 13.8368 0.890684 14.2172 1.25764C14.6175 1.64373 14.6175 2.28484 14.2172 2.67094L13.364 3.49407H14C16.2091 3.49407 18 5.28493 18 7.49407V12.9996C18 15.2087 16.2091 16.9996 14 16.9996H4C1.79086 16.9996 0 15.2087 0 12.9996V7.49406C0 5.28492 1.79086 3.49407 4 3.49407H4.58579L3.73252 2.67094ZM4 5.42343C2.89543 5.42343 2 6.31886 2 7.42343V13.0702C2 14.1748 2.89543 15.0702 4 15.0702H14C15.1046 15.0702 16 14.1748 16 13.0702V7.42343C16 6.31886 15.1046 5.42343 14 5.42343H4ZM5 9.31747C5 8.76519 5.44772 8.31747 6 8.31747C6.55228 8.31747 7 8.76519 7 9.31747V10.2115C7 10.7638 6.55228 11.2115 6 11.2115C5.44772 11.2115 5 10.7638 5 10.2115V9.31747ZM12 8.31747C11.4477 8.31747 11 8.76519 11 9.31747V10.2115C11 10.7638 11.4477 11.2115 12 11.2115C12.5523 11.2115 13 10.7638 13 10.2115V9.31747C13 8.76519 12.5523 8.31747 12 8.31747Z" fill="currentColor"></path>
            </svg>
          <?php endif; ?>
        </a>
      <?php endif; ?>
    </div>
  </div>
</header>

<div id="swup" class="transition-fade">
<main class="site-content">
