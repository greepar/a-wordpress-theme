<?php

if (!function_exists('chickensoft_blog_setup')) {
    function chickensoft_blog_setup() {
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('automatic-feed-links');
        add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));

        register_nav_menus(
            array(
                'primary' => __('Primary Menu', 'chickensoft-blog'),
            )
        );
    }
}
add_action('after_setup_theme', 'chickensoft_blog_setup');

// Register Shuoshuo (说说) Custom Post Type
function chickensoft_blog_register_shuoshuo_cpt() {
    $slug = sanitize_title(get_theme_mod('shuoshuo_slug', 'moments'));
    if (empty($slug)) $slug = 'shuoshuo';
    register_post_type('shuoshuo', array(
        'labels' => array(
            'name'               => '说说',
            'singular_name'      => '说说',
            'add_new'            => '发表说说',
            'add_new_item'       => '发表新说说',
            'edit_item'          => '编辑说说',
            'new_item'           => '新说说',
            'view_item'          => '查看说说',
            'search_items'       => '搜索说说',
            'not_found'          => '没有找到说说',
            'not_found_in_trash' => '回收站中没有说说',
            'all_items'          => '所有说说',
            'menu_name'          => '说说',
        ),
        'public'        => true,
        'has_archive'   => false,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-format-status',
        'supports'      => array('title', 'editor', 'thumbnail', 'comments'),
        'rewrite'       => array('slug' => $slug, 'with_front' => false),
        'exclude_from_search' => true,
    ));
}
add_action('init', 'chickensoft_blog_register_shuoshuo_cpt');

// Relative time for shuoshuo posts
function chickensoft_shuoshuo_relative_time($timestamp) {
    $diff = time() - $timestamp;
    if ($diff < 60) return '刚刚';
    if ($diff < 3600) return floor($diff / 60) . ' 分钟前';
    if ($diff < 86400) return floor($diff / 3600) . ' 小时前';
    if ($diff < 2592000) return floor($diff / 86400) . ' 天前';
    if ($diff < 31536000) return floor($diff / 2592000) . ' 个月前';
    return date('Y年n月j日', $timestamp);
}

function chickensoft_blog_enqueue_assets() {
    $enable_versioning = get_theme_mod('enable_asset_versioning', true);
    
    wp_enqueue_style(
        'chickensoft-blog-fonts',
        get_stylesheet_directory_uri() . '/assets/fonts/catamaran/catamaran.min.css',
        array(),
        $enable_versioning ? filemtime(get_stylesheet_directory() . '/assets/fonts/catamaran/catamaran.min.css') : null
    );

    wp_enqueue_style(
        'chickensoft-blog-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('chickensoft-blog-fonts'),
        $enable_versioning ? filemtime(get_stylesheet_directory() . '/style.css') : null
    );

    $image_brightness = absint(get_theme_mod('dark_image_brightness', 70));
    if ($image_brightness < 40) {
        $image_brightness = 40;
    } elseif ($image_brightness > 100) {
        $image_brightness = 100;
    }
    $brightness_value = $image_brightness / 100;
    wp_add_inline_style(
        'chickensoft-blog-style',
        ':root{--dark-image-brightness:' . esc_attr($brightness_value) . ';}'
    );

    wp_enqueue_script(
        'chickensoft-blog-code-copy',
        get_stylesheet_directory_uri() . '/code-copy.js',
        array(),
        $enable_versioning ? filemtime(get_stylesheet_directory() . '/code-copy.js') : null,
        true
    );

    wp_enqueue_script(
        'chickensoft-blog-progress',
        get_stylesheet_directory_uri() . '/progress.js',
        array(),
        $enable_versioning ? filemtime(get_stylesheet_directory() . '/progress.js') : null,
        true
    );



    // Swup assets are loaded lazily in footer after window.load to keep LCP path clean.

    // Unified Comments Bundle: Loader, AJAX, UI
    wp_enqueue_script(
        'chickensoft-blog-comments-bundle',
        get_stylesheet_directory_uri() . '/assets/comments-bundle.js',
        array(),
        $enable_versioning ? filemtime(get_stylesheet_directory() . '/assets/comments-bundle.js') : null,
        true
    );

    // Dequeue native comment-reply as it's now integrated in the bundle
    wp_dequeue_script('comment-reply');
    
    // Dynamic styles for Logo
    $header_icon_url = get_theme_mod('header_icon_url');
    $header_icon_hover_url = get_theme_mod('header_icon_hover_url');
    $logo_bottom_offset = get_theme_mod('logo_bottom_offset', -6);
    $logo_width = get_theme_mod('logo_width', 52);
    $logo_height = get_theme_mod('logo_height', 38);
    $logo_hover_zoom = get_theme_mod('logo_hover_zoom', true);
    
    $custom_css = '';
    if ($header_icon_url) {
        $custom_css .= '.site-logo-div { background-image: url("' . esc_url($header_icon_url) . '"); }';
    }
    if ($header_icon_hover_url) {
        $custom_css .= '.site-logo-div:hover { background-image: url("' . esc_url($header_icon_hover_url) . '") !important; }';
    }

    $custom_css .= '.site-logo-div { bottom: ' . intval($logo_bottom_offset) . 'px !important; width: ' . absint($logo_width) . 'px !important; height: ' . absint($logo_height) . 'px !important; }';
    
    if ($logo_hover_zoom) {
        $custom_css .= '.site-logo-div:hover { transform: scale(1.1); }';
    }

    if ($custom_css) {
        wp_add_inline_style('chickensoft-blog-style', $custom_css);
    }

}
add_action('wp_enqueue_scripts', 'chickensoft_blog_enqueue_assets');

function chickensoft_blog_output_theme_mode_bootstrap() {
    if (is_admin()) {
        return;
    }
    ?>
    <script>
        (function () {
            var storageKey = 'chickensoft-theme-mode';
            var root = document.documentElement;
            var mode = 'system';

            try {
                var stored = localStorage.getItem(storageKey);
                if (stored === 'light' || stored === 'dark') {
                    mode = stored;
                }
            } catch (error) {
                mode = 'system';
            }

            if (mode === 'light' || mode === 'dark') {
                root.setAttribute('data-theme', mode);
            } else {
                root.removeAttribute('data-theme');
            }

            root.setAttribute('data-theme-mode', mode);
            root.classList.remove('no-js');
        })();
    </script>
    <?php
}
add_action('wp_head', 'chickensoft_blog_output_theme_mode_bootstrap', 0);

function chickensoft_blog_lazy_load_swup() {
        if (is_admin()) {
                return;
        }

        $enable_versioning = get_theme_mod('enable_asset_versioning', true);
        $swup_init_url = get_stylesheet_directory_uri() . '/swup-init.js';
        if ($enable_versioning) {
                $swup_init_url = add_query_arg('ver', filemtime(get_stylesheet_directory() . '/swup-init.js'), $swup_init_url);
        }

        $swup_urls = array(
                get_stylesheet_directory_uri() . '/assets/lib/Swup.umd.js',
                get_stylesheet_directory_uri() . '/assets/lib/SwupProgressPlugin.umd.js',
                get_stylesheet_directory_uri() . '/assets/lib/SwupScrollPlugin.umd.js',
                get_stylesheet_directory_uri() . '/assets/lib/SwupBodyClassPlugin.umd.js',
                get_stylesheet_directory_uri() . '/assets/lib/SwupHeadPlugin.umd.js',
                $swup_init_url,
        );

        if ($enable_versioning) {
            $swup_urls[0] = add_query_arg('ver', filemtime(get_stylesheet_directory() . '/assets/lib/Swup.umd.js'), $swup_urls[0]);
            $swup_urls[1] = add_query_arg('ver', filemtime(get_stylesheet_directory() . '/assets/lib/SwupProgressPlugin.umd.js'), $swup_urls[1]);
            $swup_urls[2] = add_query_arg('ver', filemtime(get_stylesheet_directory() . '/assets/lib/SwupScrollPlugin.umd.js'), $swup_urls[2]);
            $swup_urls[3] = add_query_arg('ver', filemtime(get_stylesheet_directory() . '/assets/lib/SwupBodyClassPlugin.umd.js'), $swup_urls[3]);
            $swup_urls[4] = add_query_arg('ver', filemtime(get_stylesheet_directory() . '/assets/lib/SwupHeadPlugin.umd.js'), $swup_urls[4]);
        }

        ?>
        <script>
            (function () {
                var queue = <?php echo wp_json_encode($swup_urls); ?>;

                function loadNext(index) {
                    if (index >= queue.length) {
                        return;
                    }
                    var script = document.createElement('script');
                    script.src = queue[index];
                    script.defer = true;
                    script.onload = function () {
                        loadNext(index + 1);
                    };
                    script.onerror = function () {
                        loadNext(index + 1);
                    };
                    document.body.appendChild(script);
                }

                if (document.readyState === 'complete') {
                    loadNext(0);
                    return;
                }

                window.addEventListener('load', function () {
                    loadNext(0);
                }, { once: true });
            })();
        </script>
        <?php
}
add_action('wp_footer', 'chickensoft_blog_lazy_load_swup', 100);

function chickensoft_blog_optimize_script_loading() {
    if (is_admin()) {
        return;
    }

    // Option to disable jQuery on frontend for non-logged-in users
    if (get_theme_mod('disable_jquery_frontend', false) && !is_user_logged_in()) {
        wp_deregister_script('jquery');
        wp_deregister_script('jquery-core');
        wp_deregister_script('jquery-migrate');
    }

    $defer_handles = array(
        'jquery',
        'jquery-core',
        'jquery-migrate',
        'chickensoft-blog-code-copy',
        'chickensoft-blog-progress',
        'chickensoft-blog-safari-fix',
        'swup',
        'swup-progress-plugin',
        'swup-scroll-plugin',
        'swup-body-class-plugin',
        'swup-head-plugin',
        'chickensoft-blog-swup-init',
        'chickensoft-blog-comments-loader',
    );

    foreach ($defer_handles as $handle) {
        if (wp_script_is($handle, 'registered')) {
            wp_script_add_data($handle, 'strategy', 'defer');
        }
    }
}
add_action('wp_enqueue_scripts', 'chickensoft_blog_optimize_script_loading', 100);

/*
 * Removed async style loading to prevent Flash of Unstyled Content (FOUC).
 * The previous implementation used <link rel="preload" onload="..."> which caused the page to render twice.
 */
// function chickensoft_blog_optimize_style_delivery($html, $handle, $href, $media) { ... }
// add_filter('style_loader_tag', 'chickensoft_blog_optimize_style_delivery', 10, 4);

// Remove archive title prefix (e.g. "Category:", "Tag:")
add_filter('get_the_archive_title', function ($title) {
    if (is_category()) {
        $title = single_cat_title('', false);
    } elseif (is_tag()) {
        $title = single_tag_title('', false);
    } elseif (is_author()) {
        $title = '<span class="vcard">' . get_the_author() . '</span>';
    } elseif (is_post_type_archive()) {
        $title = post_type_archive_title('', false);
    } elseif (is_tax()) {
        $title = single_term_title('', false);
    }
    return $title;
});

function chickensoft_blog_preload_hover_image() {
    $header_icon_hover_url = get_theme_mod('header_icon_hover_url');
    // Removed preload to prevent "unused resource" console warning
}
add_action('wp_head', 'chickensoft_blog_preload_hover_image');


add_filter('show_admin_bar', '__return_false');
add_filter('edit_comment_link', '__return_empty_string');

function chickensoft_blog_register_comment_endpoints() {
    add_rewrite_rule(
        '([^/]+)/comment/?$',
        'index.php?name=$matches[1]&cs_comment=1',
        'top'
    );
    add_rewrite_rule(
        '([^/]+)/comment-form/?$',
        'index.php?name=$matches[1]&cs_comment_form=1',
        'top'
    );
    // Shuoshuo comment endpoints
    $shuoshuo_slug = sanitize_title(get_theme_mod('shuoshuo_slug', 'moments'));
    if (empty($shuoshuo_slug)) $shuoshuo_slug = 'shuoshuo';
    add_rewrite_rule(
        $shuoshuo_slug . '/([^/]+)/comment/?$',
        'index.php?shuoshuo=$matches[1]&cs_comment=1',
        'top'
    );
    add_rewrite_rule(
        $shuoshuo_slug . '/([^/]+)/comment-form/?$',
        'index.php?shuoshuo=$matches[1]&cs_comment_form=1',
        'top'
    );
    // Shuoshuo publish form (iframe endpoint)
    add_rewrite_rule(
        $shuoshuo_slug . '/publish-form/?$',
        'index.php?cs_shuoshuo_publish=1',
        'top'
    );
}
add_action('init', 'chickensoft_blog_register_comment_endpoints');

function chickensoft_blog_comment_query_vars($vars) {
    $vars[] = 'cs_comment';
    $vars[] = 'cs_comment_form';
    $vars[] = 'cs_shuoshuo_publish';
    return $vars;
}
add_filter('query_vars', 'chickensoft_blog_comment_query_vars');

function chickensoft_blog_render_comment_endpoints() {
    if (get_query_var('cs_comment')) {
        $post_id = get_queried_object_id();
        if (!$post_id) {
            $post_id = absint(get_query_var('p'));
        }
        // Fallback for shuoshuo CPT
        if (!$post_id && get_query_var('shuoshuo')) {
            $shuoshuo_posts = get_posts(array('name' => get_query_var('shuoshuo'), 'post_type' => 'shuoshuo', 'numberposts' => 1));
            if ($shuoshuo_posts) $post_id = $shuoshuo_posts[0]->ID;
        }
        if (!$post_id) {
            status_header(404);
            exit;
        }
        nocache_headers();
        header('Content-Type: text/html; charset=' . get_bloginfo('charset'));
        
        // Ensure no edit links or private info is shown in this public fragment
        add_filter('edit_comment_link', '__return_empty_string');
        remove_filter('comment_text', 'chickensoft_blog_add_comment_actions', 10);

        $comments = get_comments(array('post_id' => $post_id, 'status' => 'approve', 'order' => 'ASC'));
        if ($comments) {
            echo '<ol class="comment-list">';
            wp_list_comments(array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 40,
            ), $comments);
            echo '</ol>';
        } else {
            echo '<p class="no-comments" style="text-align:center;padding:20px;">暂无评论，快来抢沙发吧！</p>';
        }
        exit;
    }

    if (get_query_var('cs_comment_form')) {
        $post_id = get_queried_object_id();
        // Fallback for shuoshuo CPT
        if (!$post_id && get_query_var('shuoshuo')) {
            $shuoshuo_posts = get_posts(array('name' => get_query_var('shuoshuo'), 'post_type' => 'shuoshuo', 'numberposts' => 1));
            if ($shuoshuo_posts) $post_id = $shuoshuo_posts[0]->ID;
        }
        if (!$post_id) {
            status_header(404);
            exit;
        }
        $post = get_post($post_id);
        setup_postdata($post);
        $GLOBALS['post'] = $post;
        
        nocache_headers();
        header('Content-Type: text/html; charset=' . get_bloginfo('charset'));
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <?php wp_head(); ?>
            <style>
                :root {
                    --color-fd-background: #f9f3ec;
                    --color-fd-foreground: #3a2d20;
                    --color-fd-muted: #efe3d8;
                    --color-fd-muted-foreground: #746c64;
                    --color-fd-card: #fffcf6;
                    --color-fd-card-rgb: 255, 252, 246;
                    --color-fd-border: #dcccb7;
                    --color-fd-primary: #3d94d2;
                    --color-fd-accent: #ffe9b2;
                    --color-fd-accent-foreground: #3a2d20;
                    --shadow-color: 61, 46, 26;
                    --form-accent: #c0854a;
                    --form-accent-light: rgba(192, 133, 74, 0.12);
                }
                @media (prefers-color-scheme: dark) {
                    :root:not([data-theme]) {
                        --color-fd-background: #1e1816;
                        --color-fd-foreground: #ddd4bc;
                        --color-fd-muted: #2a211d;
                        --color-fd-muted-foreground: #9a8379;
                        --color-fd-card: #3c2f2a;
                        --color-fd-card-rgb: 60, 47, 42;
                        --color-fd-border: #564539;
                        --color-fd-primary: #a1ccec;
                        --color-fd-accent: #845d3a;
                        --color-fd-accent-foreground: #ddd4bc;
                        --form-accent: #d4a06a;
                        --form-accent-light: rgba(212, 160, 106, 0.15);
                    }
                }
                html[data-theme="dark"] {
                    --color-fd-background: #1e1816;
                    --color-fd-foreground: #ddd4bc;
                    --color-fd-muted: #2a211d;
                    --color-fd-muted-foreground: #9a8379;
                    --color-fd-card: #3c2f2a;
                    --color-fd-card-rgb: 60, 47, 42;
                    --color-fd-border: #564539;
                    --color-fd-primary: #a1ccec;
                    --color-fd-accent: #845d3a;
                    --color-fd-accent-foreground: #ddd4bc;
                    --form-accent: #d4a06a;
                    --form-accent-light: rgba(212, 160, 106, 0.15);
                }

                * { box-sizing: border-box; }

                body {
                    background: transparent !important;
                    padding: 0; margin: 0;
                    overflow-x: hidden;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif;
                    font-size: 0.95rem;
                    line-height: 1.6;
                    color: var(--color-fd-foreground);
                    min-height: 480px;
                }

                /* ── Reset WP chrome ── */
                #respond { margin: 0; background: transparent; border: none !important; border-top: none !important; border-radius: 0; padding: 0; box-shadow: none; width: 100%; }
                .site-header, .site-footer, #primary-nav, .comments-area { display: none !important; }
                #respond { display: block !important; }

                /* ── Title bar: avatar upload + user link ── */
                #reply-title {
                    display: flex !important;
                    align-items: center;
                    justify-content: flex-end;
                    gap: 10px;
                    margin: 0;
                    padding: 14px 24px 10px;
                    font-size: 0; /* Hide "Reply" text */
                    border: none !important;
                }
                #reply-title small { display: none; }
                #reply-title .comment-user-link {
                    display: inline-flex !important;
                    align-items: center;
                    gap: 8px;
                    text-decoration: none;
                    color: var(--color-fd-foreground);
                    font-size: 0.88rem;
                    font-weight: 600;
                    line-height: 1;
                    transition: opacity 0.2s;
                }
                #reply-title .comment-user-link:hover { opacity: 0.7; }
                #reply-title .comment-user-link img {
                    display: block;
                    flex-shrink: 0;
                    border-radius: 50%;
                    box-shadow: 0 2px 8px rgba(var(--shadow-color), 0.15);
                }
                #reply-title .comment-user-name {
                    display: inline-flex;
                    align-items: center;
                    line-height: 1;
                }
                #reply-title .comment-avatar-upload-trigger {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    gap: 6px;
                    height: 30px;
                    padding: 0 14px;
                    border: 1.5px dashed var(--color-fd-border);
                    border-radius: 999px;
                    background: transparent;
                    color: var(--color-fd-muted-foreground);
                    cursor: pointer;
                    transition: all 0.25s ease;
                    font-size: 0.75rem;
                    font-weight: 500;
                    letter-spacing: 0.02em;
                    flex-shrink: 0;
                    white-space: nowrap;
                }
                #reply-title .comment-avatar-upload-trigger:hover {
                    color: var(--form-accent);
                    border-color: var(--form-accent);
                    background: var(--form-accent-light);
                }
                #reply-title .comment-avatar-upload-trigger svg {
                    width: 14px; height: 14px;
                }

                /* ── Form container ── */
                #commentform #cs_comment_avatar { display: none; }
                #commentform {
                    padding: 6px 24px 24px;
                    margin: 0;
                    border: none !important;
                    display: flex;
                    flex-wrap: wrap;
                    gap: 16px;
                }

                /* ── Shared input/textarea base ── */
                #commentform textarea,
                #commentform input[type="text"],
                #commentform input[type="email"],
                #commentform input[type="url"] {
                    font-family: inherit;
                    font-size: 0.92rem;
                    color: var(--color-fd-foreground);
                    background: var(--form-accent-light);
                    border: 1.5px solid transparent;
                    border-radius: 12px;
                    padding: 14px 14px 10px;
                    width: 100%;
                    outline: none;
                    transition: all 0.25s ease;
                    -webkit-appearance: none;
                }
                #commentform textarea:hover,
                #commentform input[type="text"]:hover,
                #commentform input[type="email"]:hover,
                #commentform input[type="url"]:hover {
                    border-color: var(--color-fd-border);
                }
                #commentform textarea:focus,
                #commentform input[type="text"]:focus,
                #commentform input[type="email"]:focus,
                #commentform input[type="url"]:focus {
                    border-color: var(--form-accent);
                    background: var(--form-accent-light);
                    box-shadow: 0 0 0 3px rgba(192, 133, 74, 0.1);
                }
                #commentform textarea {
                    resize: vertical;
                    min-height: 100px;
                    max-height: 200px;
                }

                /* ── Floating label fields ── */
                #commentform .comment-form-comment {
                    flex: 1 1 100%;
                    position: relative;
                }
                #commentform .comment-form-author,
                #commentform .comment-form-email,
                #commentform .comment-form-url {
                    flex: 1 1 0 !important;
                    min-width: 0;
                    position: relative;
                }

                #commentform .comment-form-author label,
                #commentform .comment-form-email label,
                #commentform .comment-form-url label,
                #commentform .comment-form-comment label {
                    position: absolute;
                    left: 14px;
                    font-size: 0.88rem;
                    font-weight: 400;
                    color: var(--color-fd-muted-foreground);
                    pointer-events: none;
                    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                    transform-origin: left center;
                    margin: 0;
                    background: transparent;
                    padding: 0 4px;
                    z-index: 1;
                }
                #commentform .comment-form-author label,
                #commentform .comment-form-email label,
                #commentform .comment-form-url label {
                    top: 50%;
                    transform: translateY(-50%);
                }
                #commentform .comment-form-comment label {
                    top: 14px;
                    transform: none;
                }

                /* float up */
                #commentform .comment-form-author:focus-within label,
                #commentform .comment-form-email:focus-within label,
                #commentform .comment-form-url:focus-within label,
                #commentform .comment-form-comment:focus-within label,
                #commentform .comment-form-author.has-value label,
                #commentform .comment-form-email.has-value label,
                #commentform .comment-form-url.has-value label,
                #commentform .comment-form-comment.has-value label {
                    top: 2px;
                    transform: translateY(0) scale(0.72);
                    color: var(--form-accent);
                    background: transparent;
                    padding: 0 2px;
                }

                /* ── Cookies consent ── */
                #commentform .comment-form-cookies-consent {
                    flex: 1 1 100%;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    font-size: 0.78rem;
                    color: var(--color-fd-muted-foreground);
                    margin: -4px 0;
                    order: 9;
                }
                #commentform .comment-form-cookies-consent input[type="checkbox"] {
                    width: 16px; height: 16px;
                    accent-color: var(--form-accent);
                    border-radius: 4px;
                    flex-shrink: 0;
                }

                /* ── Captcha row ── */
                #commentform .comment-form-captcha {
                    margin: 0 !important;
                    flex: 1 1 100% !important;
                    width: 100%;
                    order: 10;
                    display: flex !important;
                    flex-wrap: wrap;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.82rem;
                    padding: 14px 16px;
                    background: var(--form-accent-light);
                    border-radius: 12px;
                    border: 1.5px solid transparent;
                    transition: border-color 0.2s;
                }
                #commentform .comment-form-captcha:focus-within {
                    border-color: var(--form-accent);
                }
                #commentform .comment-form-captcha label {
                    flex: 1 1 100%;
                    margin: 0 0 2px;
                    font-size: 0.78rem;
                    font-weight: 600;
                    color: var(--color-fd-muted-foreground);
                    text-transform: uppercase;
                    letter-spacing: 0.06em;
                }
                #commentform .comment-form-captcha .cs-captcha-row {
                    display: flex;
                    align-items: center;
                    flex-shrink: 0;
                }
                #commentform .comment-form-captcha .cs-captcha-image {
                    display: block;
                    width: 120px;
                    height: 40px;
                    border-radius: 8px;
                    border: 1px solid var(--color-fd-border);
                    cursor: pointer;
                    transition: transform 0.2s;
                }
                #commentform .comment-form-captcha .cs-captcha-image:hover {
                    transform: scale(1.04);
                }
                #commentform .comment-form-captcha input[type="text"] {
                    flex: 1;
                    min-width: 0;
                    padding: 8px 12px !important;
                    font-size: 0.88rem !important;
                    border-radius: 8px !important;
                    text-transform: uppercase;
                    letter-spacing: 2px;
                    font-weight: 600;
                    background: rgba(255,255,255,0.5) !important;
                    border: 1px solid var(--color-fd-border) !important;
                }
                #commentform .comment-form-captcha input[type="text"]:focus {
                    border-color: var(--form-accent) !important;
                    box-shadow: none !important;
                    background: rgba(255,255,255,0.7) !important;
                }

                /* ── Submit button ── */
                #commentform .form-submit {
                    margin: 0;
                    flex: 1 1 100%;
                    flex-basis: 100% !important;
                    width: 100%;
                    order: 11;
                    display: flex;
                    align-items: center;
                    justify-content: flex-end;
                    gap: 8px;
                }
                #commentform .form-submit #submit {
                    padding: 10px 28px;
                    font-size: 0.88rem;
                    font-weight: 600;
                    text-align: center;
                    white-space: nowrap;
                    border: none;
                    border-radius: 999px;
                    cursor: pointer;
                    color: #fff;
                    background: linear-gradient(135deg, var(--form-accent) 0%, #a0653a 100%);
                    box-shadow: 0 4px 14px -4px rgba(var(--shadow-color), 0.35);
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    letter-spacing: 0.03em;
                }
                #commentform .form-submit #submit:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 24px -6px rgba(var(--shadow-color), 0.45);
                    filter: brightness(1.08);
                }
                #commentform .form-submit #submit:active {
                    transform: translateY(0);
                    box-shadow: 0 2px 8px -2px rgba(var(--shadow-color), 0.3);
                }
                .cs-upload-preview {
                    flex: 1 1 100%;
                    display: none;
                    grid-template-columns: repeat(3, minmax(0, 1fr));
                    gap: 10px;
                    order: 8;
                }
                .cs-upload-preview.has-images {
                    display: grid;
                }
                .cs-upload-preview-item {
                    position: relative;
                    overflow: hidden;
                    border-radius: 12px;
                    background: var(--form-accent-light);
                    border: 1px solid var(--color-fd-border);
                    aspect-ratio: 1;
                }
                .cs-upload-preview-item img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: block;
                }
                .cs-upload-preview-remove {
                    position: absolute;
                    top: 6px;
                    right: 6px;
                    width: 24px;
                    height: 24px;
                    border: none;
                    border-radius: 50%;
                    background: rgba(0, 0, 0, 0.58);
                    color: #fff;
                    cursor: pointer;
                    font-size: 16px;
                    line-height: 24px;
                }

                /* ── Mobile responsive ── */
                @media (max-width: 520px) {
                    #commentform {
                        padding: 6px 16px 20px;
                        gap: 12px;
                    }
                    #commentform .comment-form-author,
                    #commentform .comment-form-email,
                    #commentform .comment-form-url {
                        flex: 1 1 100% !important;
                    }
                    #commentform .comment-form-captcha {
                        flex-direction: column;
                        align-items: stretch;
                    }
                    #commentform .form-submit {
                        justify-content: stretch;
                    }
                    #commentform .form-submit #submit {
                        width: 100%;
                    }
                    #reply-title {
                        padding: 12px 16px 8px;
                    }
                }

                /* ── User Comments Manager ── */
                #user-comments-manager {
                    padding: 0 24px 16px;
                }
                #user-comments-manager .comment-list .comment-body {
                    background: rgba(var(--color-fd-card-rgb, 255, 252, 246), 0.6);
                    border: 1px solid var(--color-fd-border);
                    box-shadow: none;
                }
                #user-comments-manager .children {
                    display: none;
                }
                #user-comments-manager .reply {
                    display: none !important;
                }
            </style>
            <script>
                // Debugging: Ensure form is clickable
                window.onload = function() {
                    var form = document.getElementById('commentform');
                    if (form) {
                        console.log('Comment form found');
                        form.onsubmit = function() { console.log('Form submitting...'); };
                        form.setAttribute('enctype', 'multipart/form-data');
                    } else {
                        console.error('Comment form NOT found');
                    }

                    // Floating label: toggle has-value
                    var fields = document.querySelectorAll('.comment-form-author, .comment-form-email, .comment-form-url, .comment-form-comment');
                    fields.forEach(function(field) {
                        var input = field.querySelector('input') || field.querySelector('textarea');
                        if (!input) return;
                        function toggle() {
                            field.classList.toggle('has-value', input.value.length > 0);
                        }
                        toggle();
                        input.addEventListener('input', toggle);
                        input.addEventListener('change', toggle);
                    });

                    var avatarInput = document.getElementById('cs_comment_avatar');
                    var avatarDataInput = document.getElementById('cs_comment_avatar_data');
                    var avatarTrigger = document.querySelector('.comment-avatar-upload-trigger');
                    var avatarPreview = document.querySelector('#reply-title .comment-user-link img');
                    var cookiesConsent = document.getElementById('wp-comment-cookies-consent');
                    var avatarStorageKey = 'chickensoft-comment-avatar';

                    function applyAvatarPreview(dataUrl) {
                        if (!avatarPreview || !avatarDataInput || !dataUrl) {
                            return;
                        }
                        avatarDataInput.value = dataUrl;
                        avatarPreview.src = dataUrl;
                        avatarPreview.srcset = '';
                    }

                    function persistAvatarIfAllowed() {
                        if (!avatarDataInput) {
                            return;
                        }
                        try {
                            if (cookiesConsent && cookiesConsent.checked && avatarDataInput.value) {
                                localStorage.setItem(avatarStorageKey, avatarDataInput.value);
                            } else {
                                localStorage.removeItem(avatarStorageKey);
                            }
                        } catch (error) {
                        }
                    }

                    if (avatarDataInput) {
                        try {
                            var storedAvatar = localStorage.getItem(avatarStorageKey);
                            if (storedAvatar) {
                                applyAvatarPreview(storedAvatar);
                            }
                        } catch (error) {
                        }
                    }

                    if (avatarInput && avatarTrigger) {
                        avatarTrigger.addEventListener('click', function(event) {
                            event.preventDefault();
                            avatarInput.click();
                        });

                        avatarInput.addEventListener('change', function() {
                            var file = avatarInput.files && avatarInput.files[0];
                            var maxAvatarSize = 2 * 1024 * 1024;
                            if (!file || !avatarPreview || !avatarDataInput) {
                                return;
                            }

                            if (file.size > maxAvatarSize) {
                                alert('头像文件不能超过 2MB');
                                avatarInput.value = '';
                                return;
                            }

                            if (!/^image\//i.test(file.type)) {
                                alert('请上传图片文件');
                                avatarInput.value = '';
                                return;
                            }

                            var reader = new FileReader();
                            reader.onload = function(loadEvent) {
                                var sourceImage = new Image();
                                sourceImage.onload = function() {
                                    var cropSize = Math.min(sourceImage.width, sourceImage.height);
                                    var offsetX = Math.floor((sourceImage.width - cropSize) / 2);
                                    var offsetY = Math.floor((sourceImage.height - cropSize) / 2);
                                    var canvas = document.createElement('canvas');
                                    var outputSize = 128;
                                    canvas.width = outputSize;
                                    canvas.height = outputSize;

                                    var context = canvas.getContext('2d');
                                    if (!context) {
                                        return;
                                    }

                                    context.drawImage(
                                        sourceImage,
                                        offsetX,
                                        offsetY,
                                        cropSize,
                                        cropSize,
                                        0,
                                        0,
                                        outputSize,
                                        outputSize
                                    );

                                    canvas.toBlob(function(blob) {
                                        if (!blob) {
                                            return;
                                        }

                                        var croppedDataUrl = canvas.toDataURL('image/png', 0.92);
                                        avatarDataInput.value = croppedDataUrl;

                                        var croppedFile = new File([blob], 'comment-avatar.png', {
                                            type: 'image/png',
                                            lastModified: Date.now()
                                        });
                                        var transfer = new DataTransfer();
                                        transfer.items.add(croppedFile);
                                        avatarInput.files = transfer.files;

                                        applyAvatarPreview(croppedDataUrl);
                                        persistAvatarIfAllowed();
                                    }, 'image/png', 0.92);
                                };
                                sourceImage.src = loadEvent.target.result;
                            };
                            reader.readAsDataURL(file);
                        });
                    }

                    if (form) {
                        form.addEventListener('submit', function() {
                            persistAvatarIfAllowed();
                        });
                    }

                    // Auto-resize iframe logic
                    let lastSentHeight = 0;
                    const sendHeight = () => {
                        const shell = document.getElementById('comment-form-shell');
                        if (shell) {
                            // Measuring scrollHeight which is usually more accurate for dynamic content
                            // Ensure it's at least as tall as the body content
                            const height = Math.max(shell.scrollHeight, document.body.scrollHeight) + 30; 
                            if (height !== lastSentHeight && height > 100) {
                                window.parent.postMessage({ type: 'iframe_height', height: height }, '*');
                                lastSentHeight = height;
                            }
                        }
                    };
                    
                    // Send initial height and then poll
                    setTimeout(sendHeight, 100);
                    setInterval(sendHeight, 300);
                };
            </script>
        </head>
        <body class="comment-form-iframe">
            <div id="comment-form-shell">
                <?php 
                if (isset($_GET['success']) && $_GET['success'] == '1') {
                    echo '<div style="text-align:center; padding:40px;">';
                    echo '<h3>评论发表成功！</h3>';
                    echo '<script>setTimeout(() => { window.parent.postMessage({ type: "comment_success", msg: "评论发表成功！" }, "*"); }, 1000);</script>';
                    echo '</div>';
                } else {
                    // Inject a hidden redirect_to field so wp-comments-post.php redirects back to the iframe
                    add_action('comment_form', function() use ($post_id) {
                        $redirect_url = trailingslashit(get_permalink($post_id)) . 'comment-form/?success=1';
                        echo '<input type="hidden" name="redirect_to" value="' . esc_attr($redirect_url) . '" />';
                    });
                    
                    comment_form(array(), $post_id); 
                }
                ?>

                <?php 
                // Management Section: Show user's comments for this post
                $commenter = wp_get_current_commenter();
                $user_id = get_current_user_id();
                $user_comments = array();
                
                if ($user_id || !empty($commenter['comment_author_email'])) {
                    $manager_args = array(
                        'post_id' => $post_id,
                        'status' => 'all', // Show even pending comments to the author
                    );
                    
                    if ($user_id) {
                        $manager_args['user_id'] = $user_id;
                    } else {
                        $manager_args['author_email'] = $commenter['comment_author_email'];
                    }
                    
                    if (!empty($_GET['replytocom'])) {
                        $manager_args['parent'] = intval($_GET['replytocom']);
                    }

                    $user_comments = get_comments($manager_args);
                }
                
                if (!empty($user_comments) && !isset($_GET['success'])): ?>
                    <div id="user-comments-manager" style="margin-top: 32px; padding: 0 16px 32px;">
                        <h4 style="margin-bottom: 16px; font-size: 1.1rem; opacity: 0.8;">我发表的评论 <small style="font-size: 0.78rem; font-weight: 400; opacity: 0.75;">公共评论区如未即时出现评论为cf缓存原因，请等待cf刷新</small></h4>
                        <ol class="comment-list" style="padding: 0; list-style: none;">
                            <?php 
                            wp_list_comments(array(
                                'style'       => 'ol',
                                'short_ping'  => true,
                                'avatar_size' => 32,
                            ), $user_comments);
                            ?>
                        </ol>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Edit Modal HTML (Mirroring theme's footer.php) -->
            <div id="edit-comment-modal">
                <h3 style="margin-top: 0; margin-bottom: 16px; font-size: 1.25rem; font-weight: 600;">编辑评论</h3>
                <textarea id="edit-comment-textarea"></textarea>
                <div class="edit-modal-actions">
                    <button type="button" id="cancel-edit-modal-btn" class="edit-modal-btn cancel">取消</button>
                    <button type="button" id="save-edit-modal-btn" class="edit-modal-btn save">保存修改</button>
                </div>
                <input type="hidden" id="edit-comment-id">
                <input type="hidden" id="edit-comment-nonce">
            </div>

            <?php wp_footer(); ?>
            <script>
                (function() {
                    var ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';

                    // Form loading state
                    var form = document.getElementById('commentform');
                    if (form) {
                        form.addEventListener('submit', function() {
                            var btn = document.getElementById('submit');
                            if (btn) {
                                btn.value = '提交中...';
                                setTimeout(function() { btn.disabled = true; }, 100);
                            }
                        });

                        // Add image upload button
                        var textarea = document.getElementById('comment');
                        var submitBtnParent = document.querySelector('.form-submit');
                        if (textarea && submitBtnParent) {
                            var selectedImageIds = [];
                            var hiddenImageIds = document.createElement('input');
                            hiddenImageIds.type = 'hidden';
                            hiddenImageIds.name = 'cs_comment_image_ids';

                            var previewWrap = document.createElement('div');
                            previewWrap.className = 'cs-upload-preview';

                            var imgUploadBtn = document.createElement('button');
                            imgUploadBtn.type = 'button';
                            imgUploadBtn.id = 'cs-comment-img-upload-btn';
                            imgUploadBtn.innerHTML = '<svg style="width:16px;height:16px;margin-right:4px;vertical-align:-3px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>上传图片';
                            imgUploadBtn.style.padding = '9px 18px';
                            imgUploadBtn.style.fontSize = '0.86rem';
                            imgUploadBtn.style.fontWeight = '600';
                            imgUploadBtn.style.background = 'transparent';
                            imgUploadBtn.style.color = 'var(--form-accent)';
                            imgUploadBtn.style.border = '1px dashed var(--form-accent)';
                            imgUploadBtn.style.borderRadius = '999px';
                            imgUploadBtn.style.cursor = 'pointer';
                            imgUploadBtn.style.marginRight = 'auto'; // Push submit to right
                            imgUploadBtn.style.transition = 'all 0.2s';
                            imgUploadBtn.style.display = 'inline-flex';
                            imgUploadBtn.style.alignItems = 'center';
                            
                            imgUploadBtn.addEventListener('mouseenter', function() {
                                this.style.background = 'var(--form-accent-light)';
                            });
                            imgUploadBtn.addEventListener('mouseleave', function() {
                                this.style.background = 'transparent';
                            });

                            var fileInput = document.createElement('input');
                            fileInput.type = 'file';
                            fileInput.accept = 'image/png, image/jpeg, image/gif, image/webp';
                            fileInput.style.display = 'none';

                            textarea.closest('.comment-form-comment').insertAdjacentElement('afterend', previewWrap);
                            submitBtnParent.parentNode.insertBefore(hiddenImageIds, submitBtnParent);
                            submitBtnParent.insertBefore(imgUploadBtn, submitBtnParent.firstChild);
                            submitBtnParent.insertBefore(fileInput, imgUploadBtn);

                            function syncImageIds() {
                                hiddenImageIds.value = selectedImageIds.map(function(item) { return item.id; }).join(',');
                                previewWrap.classList.toggle('has-images', selectedImageIds.length > 0);
                            }

                            function renderPreview() {
                                previewWrap.innerHTML = '';
                                selectedImageIds.forEach(function(item, index) {
                                    var node = document.createElement('div');
                                    node.className = 'cs-upload-preview-item';
                                    node.innerHTML = '<img src="' + item.url + '" alt="评论图片预览"><button type="button" class="cs-upload-preview-remove" aria-label="移除图片">&times;</button>';
                                    node.querySelector('button').addEventListener('click', function() {
                                        selectedImageIds.splice(index, 1);
                                        renderPreview();
                                        syncImageIds();
                                    });
                                    previewWrap.appendChild(node);
                                });
                            }

                            imgUploadBtn.addEventListener('click', function() {
                                if (selectedImageIds.length >= 3) {
                                    alert('评论最多上传 3 张图片');
                                    return;
                                }
                                fileInput.click();
                            });

                            fileInput.addEventListener('change', function() {
                                if (!this.files || !this.files[0]) return;
                                var file = this.files[0];
                                if (file.size > 5 * 1024 * 1024) {
                                    alert('图片太大，请选择 5MB 以下的图片');
                                    this.value = '';
                                    return;
                                }

                                var originalText = imgUploadBtn.innerHTML;
                                imgUploadBtn.innerHTML = '上传中...';
                                imgUploadBtn.disabled = true;

                                var formData = new FormData();
                                formData.append('action', 'chickensoft_upload_comment_image');
                                formData.append('nonce', '<?php echo wp_create_nonce("cs_comment_image_upload"); ?>');
                                formData.append('context', 'comment');
                                formData.append('image', file);

                                fetch(ajaxUrl, {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(function(res) { return res.json(); })
                                .then(function(data) {
                                    imgUploadBtn.innerHTML = originalText;
                                    imgUploadBtn.disabled = false;
                                    fileInput.value = ''; // Reset
                                    if (data.success) {
                                        selectedImageIds.push({ id: data.data.id, url: data.data.url });
                                        renderPreview();
                                        syncImageIds();
                                    } else {
                                        alert('上传失败: ' + (data.data || '未知错误'));
                                    }
                                })
                                .catch(function(err) {
                                    imgUploadBtn.innerHTML = originalText;
                                    imgUploadBtn.disabled = false;
                                    fileInput.value = ''; // Reset
                                    alert('网络错误，图片上传失败');
                                });
                            });
                        }
                    }

                    // Comment Management Logic
                    document.addEventListener('click', function(e) {
                        // Delete Comment
                        if (e.target.classList.contains('delete-comment-btn')) {
                            if (!confirm('确定要删除这条评论吗？')) return;
                            
                            var btn = e.target;
                            var commentId = btn.getAttribute('data-id');
                            var nonce = btn.getAttribute('data-nonce');
                            var commentEl = btn.closest('li.comment');

                            btn.disabled = true;
                            btn.textContent = '删除中...';

                            var formData = new FormData();
                            formData.append('action', 'chickensoft_delete_comment');
                            formData.append('comment_id', commentId);
                            formData.append('nonce', nonce);

                            fetch(ajaxUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then(function(res) { return res.json(); })
                            .then(function(data) {
                                if (data.success) {
                                    if (commentEl) {
                                        commentEl.style.opacity = '0.5';
                                        commentEl.innerHTML = '<div class="comment-body" style="padding: 20px; text-align: center; color: var(--color-fd-muted-foreground);">评论已删除</div>';
                                    } else {
                                        location.reload();
                                    }
                                    if (window.parent && window.parent !== window) {
                                        window.parent.postMessage({ type: 'comment_success', msg: '评论已删除' }, '*');
                                    }
                                } else {
                                    alert('删除失败: ' + (data.data || '未知错误'));
                                    btn.disabled = false;
                                    btn.textContent = '删除';
                                }
                            })
                            .catch(function(err) {
                                alert('请求失败');
                                btn.disabled = false;
                                btn.textContent = '删除';
                            });
                        }

                        // Edit Comment
                        if (e.target.classList.contains('edit-comment-btn')) {
                            var btn = e.target;
                            var commentId = btn.getAttribute('data-id');
                            var nonce = btn.getAttribute('data-nonce');
                            
                            window.currentEditingCommentBody = btn.closest('.comment-body');

                            btn.disabled = true;
                            btn.textContent = '加载中...';

                            fetch(ajaxUrl + '?action=chickensoft_get_comment&comment_id=' + commentId + '&nonce=' + nonce)
                            .then(function(res) { return res.json(); })
                            .then(function(data) {
                                btn.disabled = false;
                                btn.textContent = '编辑';

                                if (data.success) {
                                    document.body.classList.add('editing-active');
                                    var textarea = document.getElementById('edit-comment-textarea');
                                    var idInput = document.getElementById('edit-comment-id');
                                    var nonceInput = document.getElementById('edit-comment-nonce');

                                    textarea.value = data.data.content;
                                    idInput.value = commentId;
                                    nonceInput.value = nonce;
                                    textarea.focus();
                                } else {
                                    alert('无法加载评论内容: ' + (data.data || '未知错误'));
                                }
                            })
                            .catch(function(err) {
                                btn.disabled = false;
                                btn.textContent = '编辑';
                                alert('请求失败');
                            });
                        }

                        // Cancel Edit
                        if (e.target.id === 'cancel-edit-modal-btn') {
                            document.body.classList.remove('editing-active');
                        }

                        // Save Edit
                        if (e.target.id === 'save-edit-modal-btn') {
                            e.preventDefault();
                            var btn = e.target;
                            var content = document.getElementById('edit-comment-textarea').value;
                            var commentId = document.getElementById('edit-comment-id').value;
                            var nonce = document.getElementById('edit-comment-nonce').value;

                            btn.disabled = true;
                            btn.textContent = '保存中...';

                            var formData = new FormData();
                            formData.append('action', 'chickensoft_save_comment');
                            formData.append('comment_id', commentId);
                            formData.append('nonce', nonce);
                            formData.append('content', content);

                            fetch(ajaxUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then(function(res) { return res.json(); })
                            .then(function(data) {
                                btn.disabled = false;
                                btn.textContent = '保存修改';

                                if (data.success) {
                                    document.body.classList.remove('editing-active');
                                    if (window.currentEditingCommentBody) {
                                        var contentEl = window.currentEditingCommentBody.querySelector('.comment-content');
                                        if (contentEl) contentEl.innerHTML = data.data.html;
                                    } else {
                                        location.reload();
                                    }
                                    if (window.parent && window.parent !== window) {
                                        window.parent.postMessage({ type: 'comment_success', msg: '评论已更新' }, '*');
                                    }
                                } else {
                                    alert('保存失败: ' + (data.data || '未知错误'));
                                }
                            })
                            .catch(function(err) {
                                alert('请求失败');
                                btn.disabled = false;
                                btn.textContent = '保存修改';
                            });
                        }
                    });
                })();
            </script>
        </body>
        </html>
        <?php
        exit;
    }
}
add_action('template_redirect', 'chickensoft_blog_render_comment_endpoints');

// ── Shuoshuo Publish Form (iframe endpoint) ──
function chickensoft_blog_render_shuoshuo_publish_form() {
    if (!get_query_var('cs_shuoshuo_publish')) return;

    nocache_headers();
    header('Content-Type: text/html; charset=' . get_bloginfo('charset'));

    $permission = get_theme_mod('shuoshuo_publish_permission', 'owner');
    $is_allowed = false;

    if ($permission === 'public') {
        $is_allowed = true;
    } elseif ($permission === 'owner' && current_user_can('publish_posts')) {
        $is_allowed = true;
    }

    // Handle form POST submission
    $success = false;
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_allowed && isset($_POST['shuoshuo_content'])) {
        if (!wp_verify_nonce($_POST['_shuoshuo_nonce'] ?? '', 'shuoshuo_publish')) {
            $error = '安全验证失败，请刷新重试。';
        } else {
            $content = sanitize_textarea_field($_POST['shuoshuo_content']);
            $image_ids = isset($_POST['cs_shuoshuo_image_ids']) ? wp_unslash($_POST['cs_shuoshuo_image_ids']) : '';
            $image_ids = chickensoft_blog_sanitize_image_ids($image_ids, 9);
            if (empty(trim($content))) {
                $error = '内容不能为空。';
            } else {
                $post_data = array(
                    'post_type'    => 'shuoshuo',
                    'post_content' => $content,
                    'post_title'   => wp_trim_words($content, 10, '...'),
                    'post_status'  => 'publish',
                );

                // If guest (public mode), set author to site admin
                if (!is_user_logged_in()) {
                    $post_data['post_author'] = 1; // Site admin
                }

                $new_post_id = wp_insert_post($post_data, true);

                if (is_wp_error($new_post_id)) {
                    $error = '发布失败: ' . $new_post_id->get_error_message();
                } else {
                    if (!empty($image_ids)) {
                        update_post_meta($new_post_id, '_cs_shuoshuo_image_ids', $image_ids);
                        set_post_thumbnail($new_post_id, $image_ids[0]);
                    }

                    // Purge cache for the shuoshuo listing page
                    $shuoshuo_page = get_page_by_path('moments');
                    if (!$shuoshuo_page) $shuoshuo_page = get_page_by_path('shuoshuo');
                    if ($shuoshuo_page) {
                        clean_post_cache($shuoshuo_page->ID);
                    }
                    clean_post_cache($new_post_id);
                    $success = true;
                }
            }
        }
    }
    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            :root {
                --color-fd-background: #f9f3ec;
                --color-fd-foreground: #3a2d20;
                --color-fd-muted: #efe3d8;
                --color-fd-muted-foreground: #746c64;
                --color-fd-card: #fffcf6;
                --color-fd-card-rgb: 255, 252, 246;
                --color-fd-border: #dcccb7;
                --color-fd-primary: #3d94d2;
                --color-fd-accent: #ffe9b2;
                --color-fd-accent-foreground: #3a2d20;
                --shadow-color: 61, 46, 26;
                --form-accent: #c0854a;
                --form-accent-light: rgba(192, 133, 74, 0.12);
            }
            @media (prefers-color-scheme: dark) {
                :root:not([data-theme]) {
                    --color-fd-background: #1e1816;
                    --color-fd-foreground: #ddd4bc;
                    --color-fd-muted: #2a211d;
                    --color-fd-muted-foreground: #9a8379;
                    --color-fd-card: #3c2f2a;
                    --color-fd-card-rgb: 60, 47, 42;
                    --color-fd-border: #564539;
                    --color-fd-primary: #a1ccec;
                    --color-fd-accent: #845d3a;
                    --color-fd-accent-foreground: #ddd4bc;
                    --form-accent: #d4a06a;
                    --form-accent-light: rgba(212, 160, 106, 0.15);
                }
            }

            * { box-sizing: border-box; margin: 0; padding: 0; }

            body {
                background: transparent !important;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif;
                font-size: 0.95rem;
                line-height: 1.6;
                color: var(--color-fd-foreground);
                min-height: 300px;
            }

            .publish-shell {
                padding: 24px;
            }

            .publish-form {
                display: flex;
                flex-direction: column;
                gap: 16px;
            }

            .publish-form textarea {
                font-family: inherit;
                font-size: 0.95rem;
                color: var(--color-fd-foreground);
                background: var(--form-accent-light);
                border: 1.5px solid transparent;
                border-radius: 12px;
                padding: 14px;
                width: 100%;
                min-height: 140px;
                max-height: 300px;
                resize: vertical;
                outline: none;
                transition: all 0.25s ease;
                -webkit-appearance: none;
            }

            .publish-form textarea:hover {
                border-color: var(--color-fd-border);
            }

            .publish-form textarea:focus {
                border-color: var(--form-accent);
                background: var(--form-accent-light);
                box-shadow: 0 0 0 3px rgba(192, 133, 74, 0.1);
            }

            .publish-form textarea::placeholder {
                color: var(--color-fd-muted-foreground);
                opacity: 0.7;
            }

            .char-count {
                text-align: right;
                font-size: 0.78rem;
                color: var(--color-fd-muted-foreground);
                margin-top: -8px;
            }

            .char-count.is-over {
                color: #e74c3c;
                font-weight: 600;
            }

            .publish-actions {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 12px;
            }

            .publish-upload-btn {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 9px 18px;
                border: 1px dashed var(--form-accent);
                border-radius: 999px;
                background: transparent;
                color: var(--form-accent);
                cursor: pointer;
                font-size: 0.86rem;
                font-weight: 600;
                transition: all 0.2s ease;
                margin-right: auto;
            }

            .publish-upload-btn:hover {
                background: var(--form-accent-light);
            }

            .publish-upload-btn:disabled {
                cursor: not-allowed;
                opacity: 0.65;
            }

            .publish-image-preview {
                display: none;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 10px;
            }

            .publish-image-preview.has-images {
                display: grid;
            }

            .publish-image-preview-item {
                position: relative;
                overflow: hidden;
                aspect-ratio: 1;
                border-radius: 12px;
                background: var(--form-accent-light);
                border: 1px solid var(--color-fd-border);
            }

            .publish-image-preview-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .publish-image-preview-remove {
                position: absolute;
                top: 6px;
                right: 6px;
                width: 24px;
                height: 24px;
                border: none;
                border-radius: 50%;
                background: rgba(0, 0, 0, 0.58);
                color: #fff;
                cursor: pointer;
                font-size: 16px;
                line-height: 24px;
            }

            .publish-btn {
                padding: 10px 28px;
                font-size: 0.88rem;
                font-weight: 600;
                text-align: center;
                white-space: nowrap;
                border: none;
                border-radius: 999px;
                cursor: pointer;
                color: #fff;
                background: linear-gradient(135deg, var(--form-accent) 0%, #a0653a 100%);
                box-shadow: 0 4px 14px -4px rgba(var(--shadow-color), 0.35);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                letter-spacing: 0.03em;
                font-family: inherit;
            }

            .publish-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 24px -6px rgba(var(--shadow-color), 0.45);
                filter: brightness(1.08);
            }

            .publish-btn:active {
                transform: translateY(0);
                box-shadow: 0 2px 8px -2px rgba(var(--shadow-color), 0.3);
            }

            .publish-btn:disabled {
                opacity: 0.6;
                cursor: not-allowed;
                transform: none;
            }

            .publish-error {
                padding: 12px 16px;
                background: rgba(231, 76, 60, 0.1);
                border: 1px solid rgba(231, 76, 60, 0.3);
                border-radius: 12px;
                color: #c0392b;
                font-size: 0.9rem;
            }

            .publish-success {
                text-align: center;
                padding: 40px 24px;
            }

            .publish-success-icon {
                width: 56px;
                height: 56px;
                border-radius: 50%;
                background: linear-gradient(135deg, var(--form-accent) 0%, #a0653a 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 16px;
                animation: success-pop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            }

            .publish-success-icon svg {
                width: 28px;
                height: 28px;
                color: #fff;
            }

            .publish-success h3 {
                font-size: 1.15rem;
                font-weight: 600;
                margin-bottom: 6px;
            }

            .publish-success p {
                color: var(--color-fd-muted-foreground);
                font-size: 0.88rem;
            }

            @keyframes success-pop {
                0% { transform: scale(0); opacity: 0; }
                100% { transform: scale(1); opacity: 1; }
            }

            .no-permission {
                text-align: center;
                padding: 40px 24px;
                color: var(--color-fd-muted-foreground);
            }

            .no-permission svg {
                width: 48px;
                height: 48px;
                margin-bottom: 12px;
                opacity: 0.5;
            }

            .no-permission p {
                font-size: 0.95rem;
            }

            @media (max-width: 520px) {
                .publish-shell {
                    padding: 16px;
                }
                .publish-actions {
                    align-items: stretch;
                    flex-direction: column;
                }
                .publish-upload-btn,
                .publish-btn {
                    justify-content: center;
                    width: 100%;
                }
            }
        </style>
    </head>
    <body>
        <div class="publish-shell" id="publish-shell">
            <?php if ($success) : ?>
                <div class="publish-success">
                    <div class="publish-success-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <h3>发布成功！</h3>
                    <p>你的说说已经发布，页面即将刷新...</p>
                </div>
                <script>
                    setTimeout(function() {
                        window.parent.postMessage({ type: 'shuoshuo_publish_success' }, '*');
                    }, 1200);
                </script>
            <?php elseif (!$is_allowed) : ?>
                <div class="no-permission">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <p>你没有权限发布说说。</p>
                    <p style="font-size:0.82rem; margin-top: 4px;">请先登录管理员账号。</p>
                </div>
            <?php else : ?>
                <?php if (!empty($error)) : ?>
                    <div class="publish-error"><?php echo esc_html($error); ?></div>
                <?php endif; ?>
                <form class="publish-form" method="post">
                    <?php wp_nonce_field('shuoshuo_publish', '_shuoshuo_nonce'); ?>
                    <textarea name="shuoshuo_content" id="shuoshuo-content" placeholder="此刻的想法..." maxlength="2000" required><?php echo isset($_POST['shuoshuo_content']) ? esc_textarea($_POST['shuoshuo_content']) : ''; ?></textarea>
                    <div class="char-count"><span id="char-current">0</span> / 2000</div>
                    <input type="hidden" name="cs_shuoshuo_image_ids" id="cs-shuoshuo-image-ids" value="">
                    <input type="file" id="cs-shuoshuo-image-input" accept="image/png,image/jpeg,image/gif,image/webp" hidden>
                    <div class="publish-image-preview" id="cs-shuoshuo-image-preview"></div>
                    <div class="publish-actions">
                        <button type="button" class="publish-upload-btn" id="cs-shuoshuo-image-upload-btn">
                            <svg style="width:16px;height:16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            添加图片
                        </button>
                        <button type="submit" class="publish-btn" id="publish-submit">发布说说</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <script>
            // Auto-resize iframe
            (function() {
                var lastSentHeight = 0;
                function sendHeight() {
                    var shell = document.getElementById('publish-shell');
                    if (shell) {
                        var height = Math.max(shell.scrollHeight, document.body.scrollHeight) + 30;
                        if (height !== lastSentHeight && height > 50) {
                            window.parent.postMessage({ type: 'iframe_height', height: height }, '*');
                            lastSentHeight = height;
                        }
                    }
                }
                setTimeout(sendHeight, 100);
                setInterval(sendHeight, 300);

                // Character counter
                var textarea = document.getElementById('shuoshuo-content');
                var charEl = document.getElementById('char-current');
                var countWrap = document.querySelector('.char-count');
                if (textarea && charEl) {
                    function updateCount() {
                        var len = textarea.value.length;
                        charEl.textContent = len;
                        if (countWrap) {
                            countWrap.classList.toggle('is-over', len > 1800);
                        }
                    }
                    textarea.addEventListener('input', updateCount);
                    updateCount();
                }

                // Submit loading state
                var form = document.querySelector('.publish-form');
                if (form) {
                    var uploadBtn = document.getElementById('cs-shuoshuo-image-upload-btn');
                    var imageInput = document.getElementById('cs-shuoshuo-image-input');
                    var imageIdsInput = document.getElementById('cs-shuoshuo-image-ids');
                    var previewWrap = document.getElementById('cs-shuoshuo-image-preview');
                    var selectedImages = [];

                    function syncImages() {
                        if (imageIdsInput) {
                            imageIdsInput.value = selectedImages.map(function(item) { return item.id; }).join(',');
                        }
                        if (previewWrap) {
                            previewWrap.classList.toggle('has-images', selectedImages.length > 0);
                        }
                    }

                    function renderImages() {
                        if (!previewWrap) return;
                        previewWrap.innerHTML = '';
                        selectedImages.forEach(function(item, index) {
                            var node = document.createElement('div');
                            node.className = 'publish-image-preview-item';
                            node.innerHTML = '<img src="' + item.url + '" alt="说说图片预览"><button type="button" class="publish-image-preview-remove" aria-label="移除图片">&times;</button>';
                            node.querySelector('button').addEventListener('click', function() {
                                selectedImages.splice(index, 1);
                                renderImages();
                                syncImages();
                            });
                            previewWrap.appendChild(node);
                        });
                    }

                    if (uploadBtn && imageInput) {
                        uploadBtn.addEventListener('click', function() {
                            if (selectedImages.length >= 9) {
                                alert('说说最多上传 9 张图片');
                                return;
                            }
                            imageInput.click();
                        });

                        imageInput.addEventListener('change', function() {
                            if (!this.files || !this.files[0]) return;
                            var file = this.files[0];
                            if (file.size > 5 * 1024 * 1024) {
                                alert('图片太大，请选择 5MB 以下的图片');
                                this.value = '';
                                return;
                            }

                            var originalText = uploadBtn.innerHTML;
                            uploadBtn.innerHTML = '上传中...';
                            uploadBtn.disabled = true;

                            var formData = new FormData();
                            formData.append('action', 'chickensoft_upload_comment_image');
                            formData.append('nonce', '<?php echo wp_create_nonce("cs_comment_image_upload"); ?>');
                            formData.append('context', 'shuoshuo');
                            formData.append('image', file);

                            fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                                method: 'POST',
                                body: formData
                            })
                            .then(function(res) { return res.json(); })
                            .then(function(data) {
                                uploadBtn.innerHTML = originalText;
                                uploadBtn.disabled = false;
                                imageInput.value = '';
                                if (data.success) {
                                    selectedImages.push({ id: data.data.id, url: data.data.url });
                                    renderImages();
                                    syncImages();
                                    sendHeight();
                                } else {
                                    alert('上传失败: ' + (data.data || '未知错误'));
                                }
                            })
                            .catch(function() {
                                uploadBtn.innerHTML = originalText;
                                uploadBtn.disabled = false;
                                imageInput.value = '';
                                alert('网络错误，图片上传失败');
                            });
                        });
                    }

                    form.addEventListener('submit', function() {
                        var btn = document.getElementById('publish-submit');
                        if (btn) {
                            btn.textContent = '发布中...';
                            setTimeout(function() { btn.disabled = true; }, 100);
                        }
                    });
                }
            })();
        </script>
    </body>
    </html>
    <?php
    exit;
}
add_action('template_redirect', 'chickensoft_blog_render_shuoshuo_publish_form');

function chickensoft_blog_enqueue_login_assets() {
    $enable_versioning = get_theme_mod('enable_asset_versioning', true);

    wp_enqueue_style(
        'chickensoft-blog-login-fonts',
        get_stylesheet_directory_uri() . '/assets/fonts/catamaran/catamaran.css',
        array(),
        $enable_versioning ? filemtime(get_stylesheet_directory() . '/assets/fonts/catamaran/catamaran.css') : null
    );

    wp_enqueue_style(
        'chickensoft-blog-login',
        get_stylesheet_directory_uri() . '/login.css',
        array('chickensoft-blog-login-fonts'),
        filemtime(get_stylesheet_directory() . '/login.css')
    );

    wp_enqueue_script(
        'chickensoft-blog-login',
        get_stylesheet_directory_uri() . '/login.js',
        array(),
        filemtime(get_stylesheet_directory() . '/login.js'),
        true
    );
}
add_action('login_enqueue_scripts', 'chickensoft_blog_enqueue_login_assets');

function chickensoft_blog_login_logo() {
    $logo_url = get_theme_mod('header_icon_url');
    if (!$logo_url) {
        return;
    }
    echo '<style>.login h1 a { background-image: url(' . esc_url($logo_url) . ') !important; }</style>';
}
add_action('login_head', 'chickensoft_blog_login_logo');

function chickensoft_blog_login_header_url() {
    return home_url('/');
}
add_filter('login_headerurl', 'chickensoft_blog_login_header_url');

function chickensoft_blog_login_header_title() {
    return get_bloginfo('name');
}
add_filter('login_headertext', 'chickensoft_blog_login_header_title');

function chickensoft_blog_add_register_link() {
    if ( ! get_option( 'users_can_register' ) ) {
        return;
    }
    $register_url = wp_registration_url();
    if ( ! empty( $_REQUEST['redirect_to'] ) ) {
        $register_url = add_query_arg( 'redirect_to', urlencode_deep( wp_unslash( $_REQUEST['redirect_to'] ) ), $register_url );
    }
    ?>
    <div class="login-extra-links" style="margin-top: 20px; text-align: center; width: 100%;">
        <p style="margin: 0; font-size: 14px; color: var(--md-on-surface-variant);">
            Don't have an account? 
            <a href="<?php echo esc_url( $register_url ); ?>" style="color: var(--md-primary); text-decoration: none; font-weight: 600;">Register</a>
        </p>
    </div>
    <?php
}
add_action( 'login_form', 'chickensoft_blog_add_register_link' );

function chickensoft_blog_excerpt_length($length) {
    return 26;
}
add_filter('excerpt_length', 'chickensoft_blog_excerpt_length', 99);

function chickensoft_blog_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'chickensoft_blog_excerpt_more');



function chickensoft_blog_sanitize_checkbox( $checked ) {
    return ( ( isset( $checked ) && true == $checked ) ? true : false );
}

function chickensoft_blog_customize_register($wp_customize) {
    $wp_customize->add_section(
        'chickensoft_blog_header',
        array(
            'title' => __('顶部导航设置', 'chickensoft-blog'),
            'priority' => 30,
        )
    );

    $wp_customize->add_section(
        'chickensoft_blog_appearance',
        array(
            'title' => __('外观设置', 'chickensoft-blog'),
            'priority' => 35,
        )
    );

    $wp_customize->add_section(
        'chickensoft_blog_comments',
        array(
            'title' => __('评论区', 'chickensoft-blog'),
            'priority' => 36,
        )
    );

    $wp_customize->add_setting(
        'header_icon_url',
        array(
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    $wp_customize->add_control(
        'header_icon_url',
        array(
            'label' => __('顶部图标 URL', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_header',
            'type' => 'url',
        )
    );

    $wp_customize->add_setting(
        'header_icon_hover_url',
        array(
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    $wp_customize->add_control(
        'header_icon_hover_url',
        array(
            'label' => __('顶部图标悬停 URL', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_header',
            'type' => 'url',
        )
    );

    $wp_customize->add_setting(
        'logo_hover_zoom',
        array(
            'default' => true,
            'sanitize_callback' => 'chickensoft_blog_sanitize_checkbox',
        )
    );

    $wp_customize->add_control(
        'logo_hover_zoom',
        array(
            'label' => __('Logo 悬停放大', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_header',
            'type' => 'checkbox',
        )
    );

    $wp_customize->add_setting(
        'logo_bottom_offset',
        array(
            'default' => '-6',
            'sanitize_callback' => function($value) {
                return intval($value);
            }
        )
    );

    $wp_customize->add_control(
        'logo_bottom_offset',
        array(
            'label' => __('Logo 底部偏移 (px)', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_header',
            'type' => 'number',
            'input_attrs' => array(
                'min' => '-100',
                'max' => '100',
                'step' => '1',
            ),
        )
    );

    $wp_customize->add_setting(
        'logo_width',
        array(
            'default' => '52',
            'sanitize_callback' => 'absint',
        )
    );

    $wp_customize->add_control(
        'logo_width',
        array(
            'label' => __('Logo 宽度 (px)', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_header',
            'type' => 'number',
            'input_attrs' => array(
                'min' => '20',
                'max' => '200',
                'step' => '1',
            ),
        )
    );

    $wp_customize->add_setting(
        'logo_height',
        array(
            'default' => '38',
            'sanitize_callback' => 'absint',
        )
    );

    $wp_customize->add_control(
        'logo_height',
        array(
            'label' => __('Logo 高度 (px - 裁剪)', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_header',
            'type' => 'number',
            'input_attrs' => array(
                'min' => '20',
                'max' => '200',
                'step' => '1',
            ),
        )
    );

    $wp_customize->add_setting(
        'github_url',
        array(
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    $wp_customize->add_control(
        'github_url',
        array(
            'label' => __('GitHub 链接', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_header',
            'type' => 'url',
        )
    );

    $wp_customize->add_setting(
        'github_icon_url',
        array(
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    $wp_customize->add_control(
        'github_icon_url',
        array(
            'label' => __('GitHub 图标 URL', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_header',
            'type' => 'url',
        )
    );

    $wp_customize->add_setting(
        'bilibili_url',
        array(
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    $wp_customize->add_control(
        'bilibili_url',
        array(
            'label' => __('Bilibili 链接', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_header',
            'type' => 'url',
        )
    );

    $wp_customize->add_setting(
        'bilibili_icon_url',
        array(
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    $wp_customize->add_control(
        'bilibili_icon_url',
        array(
            'label' => __('Bilibili 图标 URL', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_header',
            'type' => 'url',
        )
    );

    $wp_customize->add_setting(
        'dark_image_brightness',
        array(
            'default' => 70,
            'sanitize_callback' => 'absint',
        )
    );

    $wp_customize->add_control(
        'dark_image_brightness',
        array(
            'label' => __('深色模式图片亮度 (%)', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_appearance',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 40,
                'max' => 100,
                'step' => 1,
            ),
        )
    );

    $wp_customize->add_setting(
        'enable_asset_versioning',
        array(
            'default' => true,
            'sanitize_callback' => 'chickensoft_blog_sanitize_checkbox',
        )
    );

    $wp_customize->add_control(
        'enable_asset_versioning',
        array(
            'label' => __('启用静态资源版本号 (ver)', 'chickensoft-blog'),
            'description' => __('开启后将基于文件修改时间或WordPress版本生成版本号。关闭后将移除所有CSS/JS资源中的?ver参数。', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_appearance',
            'type' => 'checkbox',
        )
    );

    // HTML Minification
    $wp_customize->add_setting(
        'enable_html_minification',
        array(
            'default' => true,
            'sanitize_callback' => 'chickensoft_blog_sanitize_checkbox',
        )
    );

    $wp_customize->add_control(
        'enable_html_minification',
        array(
            'label' => __('启用 HTML/CSS 压缩与注释移除', 'chickensoft-blog'),
            'description' => __('将压缩 HTML 输出，移除空格和 HTML/CSS 注释（如 wp-block-library-inline-css 等）。', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_appearance',
            'type' => 'checkbox',
        )
    );

    // Disable jQuery Setting
    $wp_customize->add_setting(
        'disable_jquery_frontend',
        array(
            'default' => false,
            'sanitize_callback' => 'chickensoft_blog_sanitize_checkbox',
        )
    );

    $wp_customize->add_control(
        'disable_jquery_frontend',
        array(
            'label' => __('禁用前端 jQuery 加载', 'chickensoft-blog'),
            'description' => __('如果在前端非登录状态下不使用任何依赖 jQuery 的插件，开启此选项可显著减少 JS 体积。登录状态下仍会加载 jQuery 以确保兼容性。', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_appearance',
            'type' => 'checkbox',
        )
    );

    $wp_customize->add_setting(
        'enable_comment_fragment_cf_purge',
        array(
            'default' => true,
            'sanitize_callback' => 'chickensoft_blog_sanitize_checkbox',
        )
    );

    $wp_customize->add_control(
        'enable_comment_fragment_cf_purge',
        array(
            'label' => __('评论后自动清理公共评论区 Cloudflare 缓存', 'chickensoft-blog'),
            'description' => __('控制是否在评论发布/编辑/删除/状态变化后自动清理 /comment 片段页缓存（依赖 Cloudflare Cache Manager 插件配置）。', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_comments',
            'type' => 'checkbox',
        )
    );

    $wp_customize->add_setting(
        'comment_captcha_length',
        array(
            'default' => 5,
            'sanitize_callback' => function ($value) {
                $value = absint($value);
                if ($value < 3) {
                    return 3;
                }
                if ($value > 8) {
                    return 8;
                }
                return $value;
            },
        )
    );

    $wp_customize->add_control(
        'comment_captcha_length',
        array(
            'label' => __('评论验证码位数', 'chickensoft-blog'),
            'description' => __('可设置 3-8 位。', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_comments',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 3,
                'max' => 8,
                'step' => 1,
            ),
        )
    );

    $wp_customize->add_setting(
        'comment_captcha_width',
        array(
            'default' => 160,
            'sanitize_callback' => function ($value) {
                $value = absint($value);
                if ($value < 100) {
                    return 100;
                }
                if ($value > 320) {
                    return 320;
                }
                return $value;
            },
        )
    );

    $wp_customize->add_control(
        'comment_captcha_width',
        array(
            'label' => __('评论验证码图片宽度 (px)', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_comments',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 100,
                'max' => 320,
                'step' => 1,
            ),
        )
    );

    $wp_customize->add_setting(
        'comment_captcha_height',
        array(
            'default' => 52,
            'sanitize_callback' => function ($value) {
                $value = absint($value);
                if ($value < 36) {
                    return 36;
                }
                if ($value > 120) {
                    return 120;
                }
                return $value;
            },
        )
    );

    $wp_customize->add_control(
        'comment_captcha_height',
        array(
            'label' => __('评论验证码图片高度 (px)', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_comments',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 36,
                'max' => 120,
                'step' => 1,
            ),
        )
    );

    $wp_customize->add_setting(
        'comment_captcha_font_size',
        array(
            'default' => 24,
            'sanitize_callback' => function ($value) {
                $value = absint($value);
                if ($value < 12) {
                    return 12;
                }
                if ($value > 48) {
                    return 48;
                }
                return $value;
            },
        )
    );

    $wp_customize->add_control(
        'comment_captcha_font_size',
        array(
            'label' => __('评论验证码字号 (px)', 'chickensoft-blog'),
            'description' => __('控制验证码图片内字符大小。建议 18-30。', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_comments',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 12,
                'max' => 48,
                'step' => 1,
            ),
        )
    );

    // Footer Text Customization
    $wp_customize->add_setting(
        'footer_text',
        array(
            'default' => '&copy; {year} greepar.uk &bull; {site_name} &bull; <a href="https://icp.gov.moe/?keyword=20261233" target="_blank" style="color: inherit; text-decoration: none;">萌ICP备20261233号</a>',
            'sanitize_callback' => 'wp_kses_post',
        )
    );

    $wp_customize->add_control(
        'footer_text',
        array(
            'label' => __('页脚文字', 'chickensoft-blog'),
            'description' => __('可使用 {year} 代替当前年份，{site_name} 代替网站名称。支持 HTML。', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_appearance',
            'type' => 'textarea',
        )
    );

    // Default Background Image
    $wp_customize->add_setting(
        'default_bg_image',
        array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'default_bg_image',
            array(
                'label' => __('默认背景图片', 'chickensoft-blog'),
                'description' => __('当页面没有设置特色图片背景时，将使用此图片作为背景。', 'chickensoft-blog'),
                'section' => 'chickensoft_blog_appearance',
            )
        )
    );

    $wp_customize->add_setting(
        'default_bg_blur',
        array(
            'default' => 30,
            'sanitize_callback' => 'absint',
        )
    );

    $wp_customize->add_control(
        'default_bg_blur',
        array(
            'label' => __('默认背景模糊度 (px)', 'chickensoft-blog'),
            'section' => 'chickensoft_blog_appearance',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 0,
                'max' => 100,
                'step' => 1,
            ),
        )
    );

    $wp_customize->add_setting(
        'not_found_image',
        array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'not_found_image',
            array(
                'label' => __('404 页面图片', 'chickensoft-blog'),
                'description' => __('用于 404 页面展示，你可以随时在这里更换。', 'chickensoft-blog'),
                'section' => 'chickensoft_blog_appearance',
            )
        )
    );
    // ── Shuoshuo (说说) Settings ──
    $wp_customize->add_section('chickensoft_blog_shuoshuo', array(
        'title'    => __('说说设置', 'chickensoft-blog'),
        'priority' => 37,
    ));

    $wp_customize->add_setting('shuoshuo_slug', array(
        'default'           => 'moments',
        'sanitize_callback' => 'sanitize_title',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('shuoshuo_slug', array(
        'label'       => __('URL Prefix', 'chickensoft-blog'),
        'description' => __('URL prefix for shuoshuo posts (e.g. shuoshuo, moments). After changing, go to Settings > Permalinks and click Save.', 'chickensoft-blog'),
        'section'     => 'chickensoft_blog_shuoshuo',
        'type'        => 'text',
    ));

    $wp_customize->add_setting('shuoshuo_publish_permission', array(
        'default'           => 'owner',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('shuoshuo_publish_permission', array(
        'label'       => __('谁可以发布说说', 'chickensoft-blog'),
        'description' => __('控制前端"发布说说"表单的可见性与权限。', 'chickensoft-blog'),
        'section'     => 'chickensoft_blog_shuoshuo',
        'type'        => 'select',
        'choices'     => array(
            'owner'  => __('仅管理员', 'chickensoft-blog'),
            'public' => __('所有人（公共留言墙）', 'chickensoft-blog'),
        ),
    ));
}
add_action('customize_register', 'chickensoft_blog_customize_register');

function chickensoft_blog_remove_script_version($src) {
    if (get_theme_mod('enable_asset_versioning', true)) {
        return $src;
    }
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'chickensoft_blog_remove_script_version', 15, 1);
add_filter('script_loader_src', 'chickensoft_blog_remove_script_version', 15, 1);


function chickensoft_blog_captcha_transient_key($captcha_key) {
    return 'cs_captcha_' . md5($captcha_key);
}

function chickensoft_blog_generate_captcha_code($length = 5) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $max_index = strlen($chars) - 1;
    $code = '';

    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[random_int(0, $max_index)];
    }

    return $code;
}

function chickensoft_blog_get_captcha_config() {
    $length = absint(get_theme_mod('comment_captcha_length', 5));
    $width = absint(get_theme_mod('comment_captcha_width', 160));
    $height = absint(get_theme_mod('comment_captcha_height', 52));
    $font_size = absint(get_theme_mod('comment_captcha_font_size', 24));

    if ($length < 3) {
        $length = 3;
    } elseif ($length > 8) {
        $length = 8;
    }

    if ($width < 100) {
        $width = 100;
    } elseif ($width > 320) {
        $width = 320;
    }

    if ($height < 36) {
        $height = 36;
    } elseif ($height > 120) {
        $height = 120;
    }

    if ($font_size < 12) {
        $font_size = 12;
    } elseif ($font_size > 48) {
        $font_size = 48;
    }

    // Keep font size reasonable relative to captcha height.
    $max_font_size_by_height = max(12, (int) floor($height * 0.82));
    if ($font_size > $max_font_size_by_height) {
        $font_size = $max_font_size_by_height;
    }

    return array(
        'length' => $length,
        'width' => $width,
        'height' => $height,
        'font_size' => $font_size,
    );
}

function chickensoft_blog_find_captcha_ttf_font() {
    $candidates = array(
        '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
        '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        '/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf',
        '/usr/share/fonts/dejavu/DejaVuSans.ttf',
    );

    foreach ($candidates as $path) {
        if (is_readable($path)) {
            return $path;
        }
    }

    return '';
}

function chickensoft_blog_get_captcha_image_url($captcha_key) {
    return add_query_arg(
        array(
            'action' => 'chickensoft_comment_captcha_image',
            'key' => $captcha_key,
        ),
        admin_url('admin-ajax.php')
    );
}

function chickensoft_blog_create_captcha_payload() {
    $config = chickensoft_blog_get_captcha_config();
    $captcha_key = wp_generate_password(24, false, false);
    $captcha_code = chickensoft_blog_generate_captcha_code($config['length']);

    set_transient(chickensoft_blog_captcha_transient_key($captcha_key), $captcha_code, 10 * MINUTE_IN_SECONDS);

    return array(
        'key' => $captcha_key,
        'image_url' => chickensoft_blog_get_captcha_image_url($captcha_key),
        'refresh_url' => add_query_arg('action', 'chickensoft_refresh_comment_captcha', admin_url('admin-ajax.php')),
    );
}

function chickensoft_blog_render_captcha() {
    $captcha = chickensoft_blog_create_captcha_payload();
    $config = chickensoft_blog_get_captcha_config();

    echo '<p class="comment-form-captcha">';
    echo '<label for="cs_captcha">图形验证码</label>';
    echo '<span class="cs-captcha-row">';
    echo '<img class="cs-captcha-image" src="' . esc_url($captcha['image_url']) . '" alt="验证码图片，点击刷新" data-refresh-url="' . esc_url($captcha['refresh_url']) . '" width="' . esc_attr($config['width']) . '" height="' . esc_attr($config['height']) . '" style="width:' . esc_attr($config['width']) . 'px;height:' . esc_attr($config['height']) . 'px;" loading="lazy" title="点击刷新验证码">';
    echo '</span>';
    echo '<input id="cs_captcha" name="cs_captcha" type="text" required autocomplete="off" autocapitalize="characters" spellcheck="false" maxlength="' . esc_attr($config['length']) . '" placeholder="输入图中字符">';
    echo '<input type="hidden" name="cs_captcha_key" value="' . esc_attr($captcha['key']) . '">';
    echo '</p>';
}
add_action('comment_form_after_fields', 'chickensoft_blog_render_captcha');
add_action('comment_form_logged_in_after', 'chickensoft_blog_render_captcha');

function chickensoft_blog_refresh_captcha() {
    wp_send_json_success(chickensoft_blog_create_captcha_payload());
}
add_action('wp_ajax_chickensoft_refresh_comment_captcha', 'chickensoft_blog_refresh_captcha');
add_action('wp_ajax_nopriv_chickensoft_refresh_comment_captcha', 'chickensoft_blog_refresh_captcha');

function chickensoft_blog_render_captcha_image() {
    $captcha_key = isset($_GET['key']) ? sanitize_text_field(wp_unslash($_GET['key'])) : '';
    $captcha_code = '';

    if ($captcha_key !== '') {
        $cached_code = get_transient(chickensoft_blog_captcha_transient_key($captcha_key));
        if (is_string($cached_code)) {
            $captcha_code = strtoupper($cached_code);
        }
    }

    if ($captcha_code === '') {
        status_header(404);
        $captcha_code = 'ERROR';
    }

    $config = chickensoft_blog_get_captcha_config();
    $width = $config['width'];
    $height = $config['height'];

    if (function_exists('imagecreatetruecolor') && function_exists('imagewebp')) {
        $img = imagecreatetruecolor($width, $height);
        if ($img !== false) {
            if (function_exists('imageantialias')) {
                imageantialias($img, true);
            }

            $bg = imagecolorallocate($img, 245, 246, 248);
            imagefilledrectangle($img, 0, 0, $width, $height, $bg);

            // Noise lines
            for ($i = 0; $i < 6; $i++) {
                $line_color = imagecolorallocate($img, random_int(90, 160), random_int(90, 160), random_int(90, 160));
                imageline(
                    $img,
                    random_int(0, $width - 1),
                    random_int(0, $height - 1),
                    random_int(0, $width - 1),
                    random_int(0, $height - 1),
                    $line_color
                );
            }

            // Noise dots
            for ($i = 0; $i < 28; $i++) {
                $dot_color = imagecolorallocate($img, random_int(100, 170), random_int(100, 170), random_int(100, 170));
                imagefilledellipse(
                    $img,
                    random_int(0, $width - 1),
                    random_int(0, $height - 1),
                    random_int(1, 3),
                    random_int(1, 3),
                    $dot_color
                );
            }

            $font_size = $config['font_size'];
            $letters = str_split($captcha_code);
            $count = max(1, count($letters));
            $slot = max(1, (int) floor(($width - 20) / $count));
            $ttf_font = (function_exists('imagettftext') && function_exists('imagettfbbox')) ? chickensoft_blog_find_captcha_ttf_font() : '';

            foreach ($letters as $index => $letter) {
                $text_color = imagecolorallocate($img, random_int(20, 80), random_int(20, 80), random_int(20, 80));
                if ($ttf_font !== '') {
                    $angle = random_int(-12, 12);
                    $bbox = imagettfbbox($font_size, $angle, $ttf_font, $letter);
                    $char_width = (int) max(8, abs($bbox[2] - $bbox[0]));
                    $char_height = (int) max(10, abs($bbox[1] - $bbox[7]));
                    $min_x = 8 + ($index * $slot);
                    $max_x = max($min_x, (8 + (($index + 1) * $slot) - $char_width - 2));
                    $x = random_int($min_x, $max_x);
                    $min_y = max($char_height + 2, (int) floor($height * 0.55));
                    $max_y = max($min_y, $height - 4);
                    $y = random_int($min_y, $max_y);
                    imagettftext($img, $font_size, $angle, $x, $y, $text_color, $ttf_font, $letter);
                } else {
                    // Fallback to built-in GD fonts when TTF is unavailable.
                    if ($font_size <= 10) {
                        $font = 1;
                    } elseif ($font_size <= 14) {
                        $font = 2;
                    } elseif ($font_size <= 18) {
                        $font = 3;
                    } elseif ($font_size <= 22) {
                        $font = 4;
                    } else {
                        $font = 5;
                    }
                    $char_width = imagefontwidth($font);
                    $char_height = imagefontheight($font);
                    $min_x = 10 + ($index * $slot);
                    $max_x = max($min_x, (10 + (($index + 1) * $slot) - $char_width - 2));
                    $x = random_int($min_x, $max_x);
                    $y = random_int(6, max(6, $height - $char_height - 6));
                    imagestring($img, $font, $x, $y, $letter, $text_color);
                }
            }

            ob_start();
            // quality = 0 => highest compression, smallest size.
            $encoded = imagewebp($img, null, 0);
            $binary = ob_get_clean();
            imagedestroy($img);

            if ($encoded && is_string($binary) && $binary !== '') {
                nocache_headers();
                header('Content-Type: image/webp');
                header('Content-Length: ' . strlen($binary));
                header('X-Robots-Tag: noindex, nofollow', true);
                echo $binary;
                wp_die();
            }
        }
    }

    // Fallback: output SVG if GD/WebP is unavailable on server.
    $line_nodes = '';
    $dot_nodes = '';
    $text_nodes = '';
    for ($i = 0; $i < 6; $i++) {
        $line_nodes .= sprintf(
            '<line x1="%d" y1="%d" x2="%d" y2="%d" stroke="rgba(120,120,120,0.35)" stroke-width="2" />',
            random_int(0, $width),
            random_int(0, $height),
            random_int(0, $width),
            random_int(0, $height)
        );
    }
    for ($i = 0; $i < 20; $i++) {
        $dot_nodes .= sprintf(
            '<circle cx="%d" cy="%d" r="%d" fill="rgba(120,120,120,0.3)" />',
            random_int(0, $width),
            random_int(0, $height),
            random_int(1, 2)
        );
    }
    $letters = str_split($captcha_code);
    $count = max(1, count($letters));
    $step = (int) floor(($width - 24) / $count);
    $font_size = $config['font_size'];
    foreach ($letters as $index => $letter) {
        $x = 14 + ($index * $step) + random_int(0, 6);
        $y = (int) min($height - 8, max(20, floor($height * 0.72) + random_int(-2, 3)));
        $text_nodes .= sprintf(
            '<text x="%d" y="%d" fill="rgb(40,40,40)" font-size="%d" font-family="monospace" font-weight="700">%s</text>',
            $x,
            $y,
            $font_size,
            esc_html($letter)
        );
    }
    $svg = sprintf(
        '<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 %d %d"><rect width="100%%" height="100%%" fill="rgb(245,246,248)" rx="8" ry="8" />%s%s%s</svg>',
        $width,
        $height,
        $width,
        $height,
        $line_nodes,
        $dot_nodes,
        $text_nodes
    );
    nocache_headers();
    header('Content-Type: image/svg+xml; charset=UTF-8');
    header('X-Robots-Tag: noindex, nofollow', true);
    echo $svg;
    wp_die();
}
add_action('wp_ajax_chickensoft_comment_captcha_image', 'chickensoft_blog_render_captcha_image');
add_action('wp_ajax_nopriv_chickensoft_comment_captcha_image', 'chickensoft_blog_render_captcha_image');

function chickensoft_blog_verify_captcha($commentdata) {
    if (is_admin()) {
        return $commentdata;
    }

    $comment_type = isset($commentdata['comment_type']) ? $commentdata['comment_type'] : '';
    if ($comment_type !== '' && $comment_type !== 'comment') {
        return $commentdata;
    }

    $captcha_key = isset($_POST['cs_captcha_key']) ? sanitize_text_field(wp_unslash($_POST['cs_captcha_key'])) : '';
    $captcha_input = isset($_POST['cs_captcha']) ? sanitize_text_field(wp_unslash($_POST['cs_captcha'])) : '';
    $captcha_input = preg_replace('/[^A-Za-z0-9]/', '', $captcha_input);

    if ($captcha_key === '' || $captcha_input === '') {
        wp_die('请完成验证码后再提交评论。');
    }

    $transient_key = chickensoft_blog_captcha_transient_key($captcha_key);
    $expected_code = get_transient($transient_key);
    delete_transient($transient_key);

    if (!is_string($expected_code) || $expected_code === '') {
        wp_die('验证码已过期，请刷新验证码后重试。');
    }

    if (strcasecmp((string) $expected_code, (string) $captcha_input) !== 0) {
        wp_die('验证码错误，请返回重新填写。');
    }

    return $commentdata;
}
add_filter('preprocess_comment', 'chickensoft_blog_verify_captcha');

function chickensoft_blog_get_comment_actor_emails($comment_id) {
    $comment = get_comment($comment_id);
    if (!$comment) {
        return array();
    }

    $actor_emails = array();
    if (!empty($comment->comment_author_email) && is_email($comment->comment_author_email)) {
        $actor_emails[] = strtolower(trim($comment->comment_author_email));
    }

    if (!empty($comment->user_id)) {
        $user = get_userdata((int) $comment->user_id);
        if ($user && !empty($user->user_email) && is_email($user->user_email)) {
            $actor_emails[] = strtolower(trim($user->user_email));
        }
    }

    return array_values(array_unique($actor_emails));
}

function chickensoft_blog_remove_self_notification_recipients($emails, $comment_id) {
    $actor_emails = chickensoft_blog_get_comment_actor_emails($comment_id);
    if (empty($actor_emails)) {
        return $emails;
    }

    return array_values(
        array_filter(
            array_unique($emails),
            function ($email) use ($actor_emails) {
                $normalized = strtolower(trim((string) $email));
                return $normalized !== '' && !in_array($normalized, $actor_emails, true);
            }
        )
    );
}

function chickensoft_blog_comment_notification_recipients($emails, $comment_id) {
    $comment = get_comment($comment_id);
    if (!$comment) {
        return $emails;
    }
    $post = get_post($comment->comment_post_ID);
    if ($post) {
        $author_email = get_the_author_meta('user_email', $post->post_author);
        if ($author_email && !in_array($author_email, $emails, true)) {
            $emails[] = $author_email;
        }
    }
    $admin_email = get_option('admin_email');
    if ($admin_email && !in_array($admin_email, $emails, true)) {
        $emails[] = $admin_email;
    }
    return chickensoft_blog_remove_self_notification_recipients($emails, $comment_id);
}
add_filter('comment_notification_recipients', 'chickensoft_blog_comment_notification_recipients', 10, 2);
add_filter('comment_moderation_recipients', 'chickensoft_blog_remove_self_notification_recipients', 10, 2);

function chickensoft_blog_comment_notification_notify_author($notify_author, $comment_id) {
    if (!$notify_author) {
        return $notify_author;
    }

    $comment = get_comment($comment_id);
    if (!$comment) {
        return $notify_author;
    }

    $post = get_post($comment->comment_post_ID);
    if (!$post) {
        return $notify_author;
    }

    $post_author_email = get_the_author_meta('user_email', $post->post_author);
    if (!$post_author_email || !is_email($post_author_email)) {
        return $notify_author;
    }

    $actor_emails = chickensoft_blog_get_comment_actor_emails($comment_id);
    if (in_array(strtolower(trim($post_author_email)), $actor_emails, true)) {
        return false;
    }

    return true;
}
add_filter('comment_notification_notify_author', 'chickensoft_blog_comment_notification_notify_author', 10, 2);

function chickensoft_blog_comment_form_defaults($defaults) {
    $defaults['comment_notes_before'] = '';
    $defaults['comment_notes_after'] = '';
    $defaults['logged_in_as'] = '';
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $profile_url = get_edit_profile_url($current_user->ID);
        $avatar = get_avatar($current_user->ID, 32);
        $name = $current_user->display_name;
        $url = $profile_url;
        $avatar_upload = '';
    } else {
        $avatar = get_avatar(0, 32);
        $name = '未登录';
        if (get_query_var('cs_comment_form')) {
            $url = wp_login_url(trailingslashit(get_permalink()) . 'comment-form/');
        } else {
            $url = wp_login_url(get_permalink());
        }
        $avatar_upload = '<button type="button" class="comment-avatar-upload-trigger" aria-label="上传临时头像（2MB以内，自动裁剪）" title="上传临时头像（2MB以内，自动裁剪）">' .
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>' .
            '<span>上传临时头像</span>' .
            '</button>';
    }

    $defaults['title_reply_after'] =
        $avatar_upload .
        ' <a class="comment-user-link" href="' . esc_url($url) . '">' .
        $avatar .
        '<span class="comment-user-name">' . esc_html($name) . '</span>' .
        '</a>' .
        '</h3>';
    return $defaults;
}
add_filter('comment_form_defaults', 'chickensoft_blog_comment_form_defaults');

function chickensoft_blog_render_comment_avatar_upload_field() {
    if (!get_query_var('cs_comment_form')) {
        return;
    }

    echo '<input id="cs_comment_avatar" name="cs_comment_avatar" type="file" accept="image/png,image/jpeg,image/webp,image/gif">';
    echo '<input id="cs_comment_avatar_data" name="cs_comment_avatar_data" type="hidden" value="">';
}
add_action('comment_form_after_fields', 'chickensoft_blog_render_comment_avatar_upload_field', 9);
add_action('comment_form_logged_in_after', 'chickensoft_blog_render_comment_avatar_upload_field', 9);

function chickensoft_blog_get_uploaded_comment_avatar_url($comment_id) {
    $comment_id = absint($comment_id);
    if (!$comment_id) {
        return '';
    }

    $custom_avatar = get_comment_meta($comment_id, 'comment_avatar_url', true);
    if (is_string($custom_avatar) && $custom_avatar !== '') {
        return esc_url_raw($custom_avatar);
    }

    $friend_avatar = get_comment_meta($comment_id, 'friend_avatar', true);
    if (is_string($friend_avatar) && $friend_avatar !== '') {
        return esc_url_raw($friend_avatar);
    }

    return '';
}

function chickensoft_blog_get_comment_avatar_meta_from_source($id_or_email) {
    if ($id_or_email instanceof WP_Comment) {
        return chickensoft_blog_get_uploaded_comment_avatar_url($id_or_email->comment_ID);
    }

    if (is_object($id_or_email) && isset($id_or_email->comment_ID)) {
        return chickensoft_blog_get_uploaded_comment_avatar_url($id_or_email->comment_ID);
    }

    if (is_numeric($id_or_email)) {
        return '';
    }

    return '';
}

function chickensoft_blog_save_uploaded_comment_avatar($comment_id) {
    $base64_avatar = isset($_POST['cs_comment_avatar_data']) ? trim((string) wp_unslash($_POST['cs_comment_avatar_data'])) : '';
    if ($base64_avatar !== '') {
        if (preg_match('/^data:image\/png;base64,([A-Za-z0-9+\/=]+)$/', $base64_avatar, $matches)) {
            $binary = base64_decode($matches[1], true);
            if ($binary !== false && strlen($binary) > 0 && strlen($binary) <= 2 * 1024 * 1024) {
                $upload = wp_upload_bits('comment-avatar-' . $comment_id . '.png', null, $binary);
                if (empty($upload['error']) && !empty($upload['url'])) {
                    add_comment_meta($comment_id, 'comment_avatar_url', esc_url_raw($upload['url']), true);
                    return;
                }
            }
        }
    }

    if (empty($_FILES['cs_comment_avatar']) || !isset($_FILES['cs_comment_avatar']['error'])) {
        return;
    }

    $file = $_FILES['cs_comment_avatar'];
    $max_size = 2 * 1024 * 1024;
    if ((int) $file['error'] === UPLOAD_ERR_NO_FILE) {
        return;
    }

    if ((int) $file['error'] !== UPLOAD_ERR_OK) {
        return;
    }

    if (!isset($file['size']) || (int) $file['size'] <= 0 || (int) $file['size'] > $max_size) {
        return;
    }

    if (!function_exists('wp_handle_upload')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    $overrides = array(
        'test_form' => false,
        'mimes' => array(
            'jpg|jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
        ),
    );

    $uploaded = wp_handle_upload($file, $overrides);
    if (!is_array($uploaded) || !empty($uploaded['error']) || empty($uploaded['url'])) {
        return;
    }

    add_comment_meta($comment_id, 'comment_avatar_url', esc_url_raw($uploaded['url']), true);
}
add_action('comment_post', 'chickensoft_blog_save_uploaded_comment_avatar', 8);


function chickensoft_blog_enqueue_comment_reply() {
    // Replaced with bundled version in comments-bundle.js
}
add_action('wp_enqueue_scripts', 'chickensoft_blog_enqueue_comment_reply');

function chickensoft_blog_generate_heading_ids($content, $return_headings = false) {
    $seen = array();
    $index = 0;
    $headings = array();

    $updated = preg_replace_callback(
        '/<h([2-3])([^>]*)>(.*?)<\/h\1>/is',
        function ($matches) use (&$seen, &$index, &$headings) {
            $level = (int) $matches[1];
            $attrs = $matches[2];
            $inner = $matches[3];
            $text = trim(wp_strip_all_tags($inner));
            $index++;

            $display_text = $text !== '' ? $text : 'Section ' . $index;
            $id = '';

            if (preg_match('/\bid=["\']([^"\']+)["\']/i', $attrs, $id_match)) {
                $id = $id_match[1];
                if (isset($seen[$id])) {
                    $base = $id;
                    $suffix = 2;
                    while (isset($seen[$base . '-' . $suffix])) {
                        $suffix++;
                    }
                    $id = $base . '-' . $suffix;
                    $attrs = preg_replace('/\bid=["\'][^"\']+["\']/i', 'id="' . esc_attr($id) . '"', $attrs);
                }
            } else {
                $base = sanitize_title($text);
                if ($base === '') {
                    $base = 'section-' . $index;
                }
                $id = $base;
                $suffix = 2;
                while (isset($seen[$id])) {
                    $id = $base . '-' . $suffix;
                    $suffix++;
                }
                $attrs = trim($attrs);
                $attrs = $attrs === '' ? '' : ' ' . $attrs;
                $attrs .= ' id="' . esc_attr($id) . '"';
            }

            $seen[$id] = true;
            $headings[] = array(
                'level' => $level,
                'text' => $display_text,
                'id' => $id,
            );

            return '<h' . $level . $attrs . '>' . $inner . '</h' . $level . '>';
        },
        $content
    );

    if ($return_headings) {
        return array(
            'content' => $updated,
            'headings' => $headings,
        );
    }

    return $updated;
}

function chickensoft_blog_add_heading_ids($content) {
    if (!is_singular('post')) {
        return $content;
    }
    return chickensoft_blog_generate_heading_ids($content);
}
add_filter('the_content', 'chickensoft_blog_add_heading_ids', 20);

function chickensoft_blog_get_user_agent_info($ua) {
    $os = 'Unknown OS';
    $browser = 'Unknown Browser';
    $ua = is_string($ua) ? $ua : '';

    // Detect OS
    // iOS must be checked before Mac because iOS UA often contains "like Mac OS X"
    if (preg_match('/(iphone|ipad|ipod)/i', $ua)) {
        $os = ''; // Hide OS for iOS
    } elseif (preg_match('/android\s+([\d.]+)/i', $ua, $matches)) {
        $os = 'Android ' . $matches[1];
    } elseif (preg_match('/windows nt\s+([\d.]+)/i', $ua, $matches)) {
        $ver = $matches[1];
        if ($ver == '10.0') $os = 'Windows 10+';
        elseif ($ver == '6.3') $os = 'Windows 8.1';
        elseif ($ver == '6.2') $os = 'Windows 8';
        elseif ($ver == '6.1') $os = 'Windows 7';
        else $os = 'Windows ' . $ver;
    } elseif (preg_match('/macintosh|mac os x/i', $ua)) {
        $os = ''; // Hide OS for macOS
    } elseif (preg_match('/linux/i', $ua)) {
        $os = 'Linux';
    }

    // Detect Browser
    if (preg_match('/(?:edg|edge|edga|edgios)\/([\d.]+)/i', $ua, $matches)) {
        $browser = 'Edge ' . $matches[1];
    } elseif (preg_match('/(?:firefox|fxios)\/([\d.]+)/i', $ua, $matches)) {
        $browser = 'Firefox ' . $matches[1];
    } elseif (preg_match('/(?:chrome|crios)\/([\d.]+)/i', $ua, $matches)) {
        $browser = 'Chrome ' . $matches[1];
    } elseif (preg_match('/version\/([\d.]+).*?safari/i', $ua, $matches)) {
        $browser = 'Safari ' . $matches[1];
    }

    // Simplify version numbers to Major.Minor (e.g., 14.0.1 -> 14.0)
    if ($os) {
        $os = preg_replace('/^([^\d]*\d+\.\d+)(\..*)?$/', '$1', $os);
    }
    $browser = preg_replace('/^([^\d]*\d+\.\d+)(\..*)?$/', '$1', $browser);

    return array('os' => $os, 'browser' => $browser);
}

function chickensoft_blog_add_ua_to_comment($return, $author, $comment_id) {
    if (is_admin()) return $return;
    
    $comment = get_comment($comment_id);
    if (!$comment) return $return;

    $ua_info = chickensoft_blog_get_user_agent_info($comment->comment_agent);
    $os = $ua_info['os'];
    $browser = $ua_info['browser'];
    
    if (($os === 'Unknown OS' || $os === '') && $browser === 'Unknown Browser') return $return;

    $ua_string = $browser;
    if ($os && $os !== 'Unknown OS') {
        $ua_string .= ', ' . $os;
    }

    $ua_html = sprintf(
        '<span class="comment-ua" style="margin-left: 8px; font-weight: normal; font-size: 0.85em; color: var(--color-fd-muted-foreground);">(%s)</span>',
        esc_html($ua_string)
    );
    
    return $return . $ua_html;
}
add_filter('get_comment_author_link', 'chickensoft_blog_add_ua_to_comment', 10, 3);

function chickensoft_blog_comment_form_fields($fields) {
    $commenter = wp_get_current_commenter();
    // Default to checked for guests to ensure they can manage their comments later
    $consent   = ' checked="checked"';
    
    $fields['cookies'] = sprintf(
        '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"%s /> <label for="wp-comment-cookies-consent">%s</label></p>',
        $consent,
        '在此浏览器中保存我的信息，以便下次评论时使用。'
    );
    return $fields;
}
add_filter('comment_form_default_fields', 'chickensoft_blog_comment_form_fields');

// Comment Edit/Delete Functionality
function chickensoft_blog_can_edit_comment($comment) {
    $comment = get_comment($comment);
    if (!$comment) return false;

    // Admin/Moderator
    if (current_user_can('moderate_comments')) {
        return true;
    }

    // Logged in user
    if (is_user_logged_in() && $comment->user_id == get_current_user_id()) {
        return true;
    }

    // Guest (Cookie check)
    $commenter = wp_get_current_commenter();
    if (!$comment->user_id && $commenter['comment_author_email'] && $commenter['comment_author_email'] === $comment->comment_author_email) {
        return true;
    }

    return false;
}

// Remove management actions from the public comment fragment loader
function chickensoft_blog_init_comment_fragment_check() {
    if (isset($_GET['cs_comment'])) {
        remove_filter('comment_text', 'chickensoft_blog_add_comment_actions', 10);
    }
}
add_action('wp', 'chickensoft_blog_init_comment_fragment_check');

function chickensoft_blog_add_comment_actions($content, $comment) {
    if (is_admin() || !chickensoft_blog_can_edit_comment($comment)) {
        return $content;
    }

    $nonce_edit = wp_create_nonce('chickensoft_edit_comment_' . $comment->comment_ID);
    $nonce_delete = wp_create_nonce('chickensoft_delete_comment_' . $comment->comment_ID);

    $actions = '<div class="comment-user-actions">';
    $actions .= '<button type="button" class="comment-action-btn edit-comment-btn" data-id="' . esc_attr($comment->comment_ID) . '" data-nonce="' . esc_attr($nonce_edit) . '">编辑</button>';
    $actions .= '<button type="button" class="comment-action-btn delete-comment-btn" data-id="' . esc_attr($comment->comment_ID) . '" data-nonce="' . esc_attr($nonce_delete) . '">删除</button>';
    $actions .= '</div>';

    return $content . $actions;
}
add_filter('comment_text', 'chickensoft_blog_add_comment_actions', 10, 2);

function chickensoft_blog_ajax_delete_comment() {
    $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
    check_ajax_referer('chickensoft_delete_comment_' . $comment_id, 'nonce');

    $comment = get_comment($comment_id);
    if (!$comment || !chickensoft_blog_can_edit_comment($comment)) {
        wp_send_json_error('Permission denied');
    }

    wp_trash_comment($comment_id);
    wp_send_json_success();
}
add_action('wp_ajax_chickensoft_delete_comment', 'chickensoft_blog_ajax_delete_comment');
add_action('wp_ajax_nopriv_chickensoft_delete_comment', 'chickensoft_blog_ajax_delete_comment');

function chickensoft_blog_ajax_get_comment() {
    $comment_id = isset($_GET['comment_id']) ? intval($_GET['comment_id']) : 0;
    check_ajax_referer('chickensoft_edit_comment_' . $comment_id, 'nonce');

    $comment = get_comment($comment_id);
    if (!$comment || !chickensoft_blog_can_edit_comment($comment)) {
        wp_send_json_error('Permission denied');
    }

    wp_send_json_success(array('content' => $comment->comment_content));
}
add_action('wp_ajax_chickensoft_get_comment', 'chickensoft_blog_ajax_get_comment');
add_action('wp_ajax_nopriv_chickensoft_get_comment', 'chickensoft_blog_ajax_get_comment');

function chickensoft_blog_ajax_save_comment() {
    $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
    check_ajax_referer('chickensoft_edit_comment_' . $comment_id, 'nonce');

    $comment = get_comment($comment_id);
    if (!$comment || !chickensoft_blog_can_edit_comment($comment)) {
        wp_send_json_error('Permission denied');
    }

    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    if (empty($content)) {
        wp_send_json_error('Content cannot be empty');
    }

    $updated = wp_update_comment(array(
        'comment_ID' => $comment_id,
        'comment_content' => $content
    ));

    if ($updated) {
        // Get the updated content with formatting applied
        $updated_comment = get_comment($comment_id);
        $formatted_content = apply_filters('comment_text', $updated_comment->comment_content, $updated_comment);
        // Remove the actions buttons from the response to avoid duplication (they are added by JS or filter)
        // Actually, apply_filters('comment_text') will add the buttons again.
        // We want to return the HTML including buttons so we can replace the container.
        wp_send_json_success(array('html' => $formatted_content));
    } else {
        wp_send_json_error('Update failed');
    }
}
add_action('wp_ajax_chickensoft_save_comment', 'chickensoft_blog_ajax_save_comment');
add_action('wp_ajax_nopriv_chickensoft_save_comment', 'chickensoft_blog_ajax_save_comment');



// Handle Cloudflare Verification Request
function chickensoft_handle_cf_verify() {
    if (isset($_GET['cf_verify']) && $_GET['cf_verify'] === '1') {
        status_header(200);
        // No-cache headers to ensure we don't cache the result if it was somehow intercepted incorrectly before
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Verification Success</title><style>body{display:flex;justify-content:center;align-items:center;height:100vh;margin:0;font-family:sans-serif;background:#f0fdf4;color:#166534;flex-direction:column;}.icon{font-size:48px;margin-bottom:16px;}</style></head><body><div class="icon">✅</div><div id="cf-success" style="font-size:18px;font-weight:bold;">验证成功 Verification Passed</div><p>窗口将自动关闭...</p></body></html>';
        exit;
    }
}
add_action('init', 'chickensoft_handle_cf_verify');


// Use local avatar to completely avoid Gravatar requests
function chickensoft_use_local_avatar_only($args, $id_or_email) {
    $custom_avatar_url = chickensoft_blog_get_comment_avatar_meta_from_source($id_or_email);
    if ($custom_avatar_url !== '') {
        $args['url'] = $custom_avatar_url;
        return $args;
    }

    // Only override if no URL is already set (e.g. by another plugin)
    if (empty($args['url'])) {
        $args['url'] = get_stylesheet_directory_uri() . '/assets/images/default-avatar.png';
    }
    return $args;
}
add_filter('pre_get_avatar_data', 'chickensoft_use_local_avatar_only', 100, 2);

// Remove the default "/category" base from category archive URLs.
// Remove category base
function chickensoft_blog_remove_category_base() {
    global $wp_rewrite;
    // Change the structure to remove /category/
    if (isset($wp_rewrite->extra_permastructs['category'])) {
        $wp_rewrite->extra_permastructs['category']['struct'] = '%category%';
    }
}
add_action('init', 'chickensoft_blog_remove_category_base');

// Add specific rewrite rules for each category to prevent conflicts with pages/posts
function chickensoft_blog_no_category_base_rewrite_rules($category_rewrite) {
    $category_rewrite = array();
    $categories = get_categories(array('hide_empty' => false));
    foreach ($categories as $category) {
        $category_nicename = $category->slug;
        if ($category->parent == $category->cat_ID) {
            $category->parent = 0;
        } elseif ($category->parent != 0) {
            $category_nicename = get_category_parents($category->parent, false, '/', true) . $category_nicename;
        }
        $category_rewrite['(' . $category_nicename . ')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
        $category_rewrite['(' . $category_nicename . ')/page/?([0-9]{1,})/?$'] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
        $category_rewrite['(' . $category_nicename . ')/?$'] = 'index.php?category_name=$matches[1]';
    }
    return $category_rewrite;
}
add_filter('category_rewrite_rules', 'chickensoft_blog_no_category_base_rewrite_rules');

// Flush rules once if needed
function chickensoft_blog_flush_category_rules_once() {
    if (get_option('chickensoft_blog_category_base_flushed_v2')) {
       return;
    }
    // Ensure struct is set before flushing
    chickensoft_blog_remove_category_base();
    flush_rewrite_rules();
    update_option('chickensoft_blog_category_base_flushed_v2', 1);
}
add_action('init', 'chickensoft_blog_flush_category_rules_once', 20);

add_filter( 'wp_calculate_image_srcset', '__return_false' );

// Save friend link avatar as comment meta
function chickensoft_save_comment_friend_avatar($comment_id) {
    if (isset($_POST['friend_avatar'])) {
        $avatar = sanitize_url($_POST['friend_avatar']);
        add_comment_meta($comment_id, 'friend_avatar', $avatar, true);
    }

    // Check if this is a Friend Link application (simple check via comment content or POST field)
    // Here we check if the comment content contains the marker we added in JS.
    $comment = get_comment($comment_id);
    if ($comment && strpos($comment->comment_content, '### 友链申请') !== false) {
        
        // 1. Send Email Notification to Applicant
        $to = $comment->comment_author_email;
        if (is_email($to)) {
            $subject = '【' . get_bloginfo('name') . '】您的友链申请已收到';
            $message = "您好 {$comment->comment_author}，\n\n";
            $message .= "我们已收到您在 " . get_bloginfo('name') . " 提交的友链申请。\n";
            $message .= "审核通过后，我们会尽快将您的站点加入友链列表。\n\n";
            $message .= "申请详情：\n";
            $message .= $comment->comment_content . "\n\n";
            $message .= "祝生活愉快！\n";
            $message .= get_bloginfo('url');

            $headers = array('Content-Type: text/plain; charset=UTF-8');
            wp_mail($to, $subject, $message, $headers);
        }
    }
}
add_action('comment_post', 'chickensoft_save_comment_friend_avatar');

// Add query param to redirect after friend link submission
function chickensoft_friend_link_redirect($location, $comment) {
    if (strpos($comment->comment_content, '### 友链申请') !== false) {
        return add_query_arg('friend_submit', 'success', $location);
    }
    return $location;
}
add_filter('comment_post_redirect', 'chickensoft_friend_link_redirect', 10, 2);

// Purge Cloudflare cache for public comment fragment endpoint (/comment)
function chickensoft_is_comment_fragment_cf_purge_enabled() {
    return (bool) get_theme_mod('enable_comment_fragment_cf_purge', true);
}

function chickensoft_get_cf_cache_manager_settings() {
    if (!chickensoft_is_comment_fragment_cf_purge_enabled()) {
        return null;
    }

    if (!class_exists('CloudflareCacheManager')) {
        return null;
    }

    $settings = get_option('cf_cache_manager_settings', array());
    if (!is_array($settings)) {
        return null;
    }

    if (empty($settings['zone_id']) || empty($settings['api_key'])) {
        return null;
    }

    return $settings;
}

function chickensoft_purge_cf_urls_via_cache_manager(array $urls) {
    $settings = chickensoft_get_cf_cache_manager_settings();
    if (!$settings) {
        return;
    }

    $urls = array_values(array_unique(array_filter(array_map('esc_url_raw', $urls))));
    if (empty($urls)) {
        return;
    }

    $headers = array(
        'Content-Type' => 'application/json',
    );

    if (!empty($settings['use_token'])) {
        $headers['Authorization'] = 'Bearer ' . $settings['api_key'];
    } else {
        if (empty($settings['email'])) {
            return;
        }
        $headers['X-Auth-Email'] = $settings['email'];
        $headers['X-Auth-Key'] = $settings['api_key'];
    }

    $response = wp_remote_post(
        'https://api.cloudflare.com/client/v4/zones/' . rawurlencode($settings['zone_id']) . '/purge_cache',
        array(
            'timeout' => 20,
            'headers' => $headers,
            'body' => wp_json_encode(array('files' => $urls)),
        )
    );

    if (defined('WP_DEBUG') && WP_DEBUG) {
        if (is_wp_error($response)) {
            error_log('[chickensoft-blog] CF purge failed: ' . $response->get_error_message());
            return;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code < 200 || $status_code >= 300) {
            error_log('[chickensoft-blog] CF purge unexpected status: ' . $status_code . ' body=' . wp_remote_retrieve_body($response));
        }
    }
}

function chickensoft_purge_public_comment_fragment_cache($post_id) {
    $post_id = absint($post_id);
    if (!$post_id) {
        return;
    }

    $post_url = get_permalink($post_id);
    if (!$post_url) {
        return;
    }

    $comment_url = trailingslashit($post_url) . 'comment/';
    $urls_to_purge = array(
        $comment_url,
        untrailingslashit($comment_url),
    );

    chickensoft_purge_cf_urls_via_cache_manager($urls_to_purge);
}
add_action('chickensoft_purge_public_comment_fragment_cache', 'chickensoft_purge_public_comment_fragment_cache', 10, 1);

function chickensoft_schedule_public_comment_fragment_cache_purge($post_id) {
    if (!chickensoft_is_comment_fragment_cf_purge_enabled()) {
        return;
    }

    $post_id = absint($post_id);
    if (!$post_id) {
        return;
    }

    // Purge immediately on comment changes.
    chickensoft_purge_public_comment_fragment_cache($post_id);
}

function chickensoft_schedule_comment_fragment_purge_on_new_comment($comment_id, $comment_approved) {
    if ($comment_approved !== 1 && $comment_approved !== '1' && $comment_approved !== 'approve') {
        return;
    }

    $comment = get_comment($comment_id);
    if (!$comment) {
        return;
    }

    chickensoft_schedule_public_comment_fragment_cache_purge($comment->comment_post_ID);
}
add_action('comment_post', 'chickensoft_schedule_comment_fragment_purge_on_new_comment', 20, 2);

function chickensoft_schedule_comment_fragment_purge_by_comment_id($comment_id) {
    $comment = get_comment($comment_id);
    if (!$comment) {
        return;
    }

    chickensoft_schedule_public_comment_fragment_cache_purge($comment->comment_post_ID);
}
add_action('edit_comment', 'chickensoft_schedule_comment_fragment_purge_by_comment_id', 20, 1);
add_action('deleted_comment', 'chickensoft_schedule_comment_fragment_purge_by_comment_id', 20, 1);
add_action('trashed_comment', 'chickensoft_schedule_comment_fragment_purge_by_comment_id', 20, 1);
add_action('spammed_comment', 'chickensoft_schedule_comment_fragment_purge_by_comment_id', 20, 1);

function chickensoft_schedule_comment_fragment_purge_on_status_change($new_status, $old_status, $comment) {
    if ($new_status === $old_status || !$comment) {
        return;
    }

    chickensoft_schedule_public_comment_fragment_cache_purge($comment->comment_post_ID);
}
add_action('transition_comment_status', 'chickensoft_schedule_comment_fragment_purge_on_status_change', 20, 3);

function chickensoft_redirect_not_found_to_404_slug() {
    if (is_admin() || wp_doing_ajax() || !is_404()) {
        return;
    }

    $request_path = isset($_SERVER['REQUEST_URI']) ? wp_parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
    $request_path = is_string($request_path) ? trim($request_path, '/') : '';
    if ($request_path === '404') {
        return;
    }

    wp_safe_redirect(home_url('/404'), 302);
    exit;
}
add_action('template_redirect', 'chickensoft_redirect_not_found_to_404_slug', 5);

function chickensoft_blog_disable_frontend_html_cache_reuse() {
    if (is_admin() || wp_doing_ajax()) {
        return;
    }

    if (defined('REST_REQUEST') && REST_REQUEST) {
        return;
    }

    if (is_feed() || is_trackback() || is_robots() || is_favicon()) {
        return;
    }

    nocache_headers();
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
}
add_action('send_headers', 'chickensoft_blog_disable_frontend_html_cache_reuse', 20);

// --- Chickensoft Featured Image Background ---

// 1. Add Meta Box
function chickensoft_add_featured_bg_meta_box() {
    add_meta_box(
        'chickensoft_featured_bg_meta', // ID
        __('Featured Image Background', 'chickensoft-blog'), // Title
        'chickensoft_render_featured_bg_meta_box', // Callback
        ['post', 'page'], // Screen
        'side', // Context
        'default' // Priority
    );
}
add_action('add_meta_boxes', 'chickensoft_add_featured_bg_meta_box');

// 2. Render Meta Box
function chickensoft_render_featured_bg_meta_box($post) {
    // Nonce for verification
    wp_nonce_field('chickensoft_save_featured_bg_meta', 'chickensoft_featured_bg_nonce');
    
    $enabled = get_post_meta($post->ID, '_chickensoft_featured_bg_enabled', true);
    $checked = $enabled === 'yes' ? 'checked' : '';
    
    $blur = get_post_meta($post->ID, '_chickensoft_featured_bg_blur', true);
    if ($blur === '') $blur = 30; // default
    ?>
    <p>
        <label for="chickensoft_featured_bg">
            <input type="checkbox" name="chickensoft_featured_bg" id="chickensoft_featured_bg" value="yes" <?php echo $checked; ?>>
            <?php _e('Set featured image as background', 'chickensoft-blog'); ?>
        </label>
    </p>
    
    <p>
        <label for="chickensoft_featured_bg_blur"><?php _e('Blur Amount (px):', 'chickensoft-blog'); ?></label><br>
        <input type="number" name="chickensoft_featured_bg_blur" id="chickensoft_featured_bg_blur" value="<?php echo esc_attr($blur); ?>" min="0" max="100" step="1" style="width: 100%;">
    </p>

    <p class="description">
        <?php _e('If enabled, the featured image will be used as a background. Adjust blur amount as needed (default 30px).', 'chickensoft-blog'); ?>
    </p>
    <?php
}

// 3. Save Meta Box
function chickensoft_save_featured_bg_meta($post_id) {
    if (!isset($_POST['chickensoft_featured_bg_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['chickensoft_featured_bg_nonce'], 'chickensoft_save_featured_bg_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (isset($_POST['chickensoft_featured_bg']) && $_POST['chickensoft_featured_bg'] === 'yes') {
        update_post_meta($post_id, '_chickensoft_featured_bg_enabled', 'yes');
    } else {
        delete_post_meta($post_id, '_chickensoft_featured_bg_enabled');
    }

    if (isset($_POST['chickensoft_featured_bg_blur'])) {
        $blur = absint($_POST['chickensoft_featured_bg_blur']);
        update_post_meta($post_id, '_chickensoft_featured_bg_blur', $blur);
    }
}
add_action('save_post', 'chickensoft_save_featured_bg_meta');

// 4. Helper to get background settings
function chickensoft_get_current_bg_settings() {
    $settings = [
        'enabled' => false,
        'url' => '',
        'blur' => 30
    ];

    if (is_singular(['post', 'page'])) {
        global $post;
        if ($post) {
            $enabled = get_post_meta($post->ID, '_chickensoft_featured_bg_enabled', true);
            if ($enabled === 'yes' && has_post_thumbnail($post->ID)) {
                $settings['enabled'] = true;
                $settings['url'] = get_the_post_thumbnail_url($post->ID, 'full');
                $blur = get_post_meta($post->ID, '_chickensoft_featured_bg_blur', true);
                if ($blur !== '') $settings['blur'] = (int)$blur;
                return $settings;
            }
        }
    }

    // Fallback to theme default background
    $default_url = get_theme_mod('default_bg_image');
    if ($default_url) {
        $settings['enabled'] = true;
        $settings['url'] = $default_url;
        $settings['blur'] = (int)get_theme_mod('default_bg_blur', 30);
    }

    return $settings;
}

// 5. Frontend Output (CSS + HTML)
function chickensoft_output_featured_bg_css() {
    $bg = chickensoft_get_current_bg_settings();
    if (!$bg['enabled'] || !$bg['url']) {
        return;
    }
    
    // Define overlay colors based on style.css
    // Light: #f9f3ec
    // Dark: #1e1816
    ?>
    <link rel="preload" as="image" href="<?php echo esc_url($bg['url']); ?>" fetchpriority="high">
    <style>
        /* Ensure html/body background doesn't hide the fixed image */
        html.has-featured-bg-active, 
        body.has-featured-bg {
            background-color: transparent !important;
            background-image: none !important;
        }

        /* The blurred background image container */
        .featured-bg-layer {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: -20;
            pointer-events: none;
        }

        .featured-bg-image {
            position: absolute;
            top: -50px; left: -50px; right: -50px; bottom: -50px; /* Extend to avoid blur edges */
            background-image: url('<?php echo esc_url($bg['url']); ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: blur(<?php echo intval($bg['blur']); ?>px) brightness(0.9);
            opacity: 1;
        }
        
        /* Overlay for readability + adaptation */
        .featured-bg-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(249, 243, 236, 0.75); /* Light mode overlay */
        }
        
        html[data-theme="dark"] .featured-bg-overlay {
            background-color: rgba(30, 24, 22, 0.85); /* Dark mode overlay */
        }

        @media (prefers-color-scheme: dark) {
            html:not([data-theme]) .featured-bg-overlay {
                background-color: rgba(30, 24, 22, 0.85); /* Dark mode overlay */
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'chickensoft_output_featured_bg_css');

function chickensoft_inject_featured_bg_html() {
    // Always output the container so Swup can find it and replace its contents
    echo '<div id="featured-bg-layer" class="featured-bg-layer">';
    $bg = chickensoft_get_current_bg_settings();
    if ($bg['enabled'] && $bg['url']) {
        echo '<div class="featured-bg-image"></div><div class="featured-bg-overlay"></div>';
    }
    echo '</div>';
}
add_action('wp_body_open', 'chickensoft_inject_featured_bg_html');

// 6. Add body class
function chickensoft_add_featured_bg_body_class($classes) {
    $bg = chickensoft_get_current_bg_settings();
    if ($bg['enabled'] && $bg['url']) {
        $classes[] = 'has-featured-bg';
    }
    return $classes;
}
add_filter('body_class', 'chickensoft_add_featured_bg_body_class');

// Add class to html as well to ensure background transparency works up the chain if needed
function chickensoft_add_featured_bg_html_class($output) {
    $bg = chickensoft_get_current_bg_settings();
    if ($bg['enabled'] && $bg['url']) {
        $output .= ' class="has-featured-bg-active"';
    }
    return $output;
}
// Note: language_attributes filter is tricky, often it's just 'lang="en"', not containing class.
// Instead, I'll use a small JS fix if CSS isn't enough, but usually targeting body is sufficient

// --- Comment and Shuoshuo Image Uploads ---

function chickensoft_blog_sanitize_image_ids($value, $limit = 9) {
    if (is_string($value)) {
        $value = explode(',', $value);
    }
    if (!is_array($value)) {
        return array();
    }

    $ids = array();
    foreach ($value as $id) {
        $id = absint($id);
        if (!$id || in_array($id, $ids, true)) {
            continue;
        }
        if (wp_attachment_is_image($id)) {
            $ids[] = $id;
        }
        if (count($ids) >= $limit) {
            break;
        }
    }

    return $ids;
}

function chickensoft_blog_get_image_upload_context() {
    $context = isset($_POST['context']) ? sanitize_key(wp_unslash($_POST['context'])) : 'comment';
    return $context === 'shuoshuo' ? 'shuoshuo' : 'comment';
}

function chickensoft_comment_image_upload_dir($upload) {
    $context = chickensoft_blog_get_image_upload_context();
    $folder = $context === 'shuoshuo' ? 'shuoshuo-images' : 'comment-images';
    $upload['subdir'] = '/' . $folder . '/' . date('Y/m');
    $upload['path']   = $upload['basedir'] . $upload['subdir'];
    $upload['url']    = $upload['baseurl'] . $upload['subdir'];
    return $upload;
}

function chickensoft_blog_render_image_gallery(array $image_ids, $class_name = 'cs-image-gallery', $size = 'medium_large') {
    $image_ids = chickensoft_blog_sanitize_image_ids($image_ids, 9);
    if (empty($image_ids)) {
        return '';
    }

    $count = count($image_ids);
    $html = '<div class="' . esc_attr($class_name) . ' ' . esc_attr($class_name . '--count-' . min($count, 9)) . '" data-count="' . esc_attr($count) . '">';
    foreach ($image_ids as $image_id) {
        $full_url = wp_get_attachment_image_url($image_id, 'full');
        if (!$full_url) {
            continue;
        }

        $html .= '<a class="' . esc_attr($class_name . '__item') . '" href="' . esc_url($full_url) . '" data-cs-lightbox="image">';
        $html .= wp_get_attachment_image(
            $image_id,
            $size,
            false,
            array(
                'class' => $class_name . '__image',
                'loading' => 'lazy',
            )
        );
        $html .= '</a>';
    }
    $html .= '</div>';

    return $html;
}

function chickensoft_blog_get_shuoshuo_image_ids($post_id = null) {
    $post_id = $post_id ? absint($post_id) : get_the_ID();
    if (!$post_id) {
        return array();
    }

    $image_ids = get_post_meta($post_id, '_cs_shuoshuo_image_ids', true);
    $image_ids = chickensoft_blog_sanitize_image_ids($image_ids, 9);

    if (empty($image_ids) && has_post_thumbnail($post_id)) {
        $image_ids[] = get_post_thumbnail_id($post_id);
    }

    return $image_ids;
}

function chickensoft_blog_render_shuoshuo_images($post_id = null, $size = 'medium_large') {
    return chickensoft_blog_render_image_gallery(chickensoft_blog_get_shuoshuo_image_ids($post_id), 'shuoshuo-image-gallery', $size);
}

function chickensoft_blog_render_comment_images($content, $comment) {
    if (!$comment instanceof WP_Comment) {
        return $content;
    }

    $image_ids = get_comment_meta($comment->comment_ID, '_cs_comment_image_ids', true);
    $gallery = chickensoft_blog_render_image_gallery(chickensoft_blog_sanitize_image_ids($image_ids, 3), 'comment-image-gallery', 'medium');
    if ($gallery === '') {
        return $content;
    }

    return $content . $gallery;
}
add_filter('comment_text', 'chickensoft_blog_render_comment_images', 9, 2);

function chickensoft_blog_save_comment_images($comment_id) {
    $image_ids = isset($_POST['cs_comment_image_ids']) ? wp_unslash($_POST['cs_comment_image_ids']) : '';
    $image_ids = chickensoft_blog_sanitize_image_ids($image_ids, 3);
    if (!empty($image_ids)) {
        update_comment_meta($comment_id, '_cs_comment_image_ids', $image_ids);
    }
}
add_action('comment_post', 'chickensoft_blog_save_comment_images', 9);

add_action('wp_ajax_chickensoft_upload_comment_image', 'chickensoft_ajax_upload_comment_image');
add_action('wp_ajax_nopriv_chickensoft_upload_comment_image', 'chickensoft_ajax_upload_comment_image');

function chickensoft_ajax_upload_comment_image() {
    check_ajax_referer('cs_comment_image_upload', 'nonce');

    if (empty($_FILES['image'])) {
        wp_send_json_error('请选择要上传的图片');
    }

    $file = $_FILES['image'];
    if (!isset($file['size']) || (int) $file['size'] <= 0) {
        wp_send_json_error('图片文件无效');
    }

    if ((int) $file['size'] > 5 * 1024 * 1024) {
        wp_send_json_error('图片过大，最大允许 5MB');
    }

    $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
    $wp_filetype = wp_check_filetype_and_ext($file['tmp_name'], $file['name']);
    if (empty($wp_filetype['type']) || !in_array($wp_filetype['type'], $allowed_types, true)) {
        wp_send_json_error('仅支持 JPG, PNG, GIF, WEBP 格式');
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';

    add_filter('upload_dir', 'chickensoft_comment_image_upload_dir');
    $movefile = wp_handle_upload($file, array('test_form' => false));
    remove_filter('upload_dir', 'chickensoft_comment_image_upload_dir');

    if (!is_array($movefile) || isset($movefile['error'])) {
        wp_send_json_error(isset($movefile['error']) ? $movefile['error'] : '上传失败');
    }

    $attachment = array(
        'post_mime_type' => $movefile['type'],
        'post_title' => sanitize_file_name(pathinfo($movefile['file'], PATHINFO_FILENAME)),
        'post_content' => '',
        'post_status' => 'inherit',
    );
    $attachment_id = wp_insert_attachment($attachment, $movefile['file']);
    if (is_wp_error($attachment_id) || !$attachment_id) {
        wp_send_json_error('附件创建失败');
    }

    $metadata = wp_generate_attachment_metadata($attachment_id, $movefile['file']);
    if (!is_wp_error($metadata) && !empty($metadata)) {
        wp_update_attachment_metadata($attachment_id, $metadata);
    }

    wp_send_json_success(array(
        'id' => $attachment_id,
        'url' => wp_get_attachment_image_url($attachment_id, 'medium') ?: $movefile['url'],
        'full_url' => wp_get_attachment_image_url($attachment_id, 'full') ?: $movefile['url'],
        'width' => isset($metadata['width']) ? absint($metadata['width']) : 0,
        'height' => isset($metadata['height']) ? absint($metadata['height']) : 0,
    ));
}
 
// IF html doesn't have a background. Use :root or html selector in CSS.
// I added html.has-featured-bg-active to CSS above, but adding class to HTML tag in PHP is harder securely without output buffering or JS.
// However, style.css sets background on :root via `html { background: var(...) }`.
// So I need to override `html` background.
// I will just use `html` selector in the style block inside body.


// 7. LCP Priority (Preload Logo)
function chickensoft_lcp_priority_preloads() {
    $header_icon_url = get_theme_mod('header_icon_url');
    if ($header_icon_url) {
        echo '<link rel="preload" as="image" href="' . esc_url($header_icon_url) . '" fetchpriority="high">';
    }

    // Preload Featured Image on single posts if no background is used
    if (is_singular() && has_post_thumbnail()) {
        $featured_bg = chickensoft_get_current_bg_settings();
        if (!$featured_bg['enabled'] || !$featured_bg['url']) {
            $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
            if ($thumbnail_url) {
                echo '<link rel="preload" as="image" href="' . esc_url($thumbnail_url) . '" fetchpriority="high">';
            }
        }
    }
}
add_action('wp_head', 'chickensoft_lcp_priority_preloads', 5);

// 8. HTML Minification
function chickensoft_blog_minify_html($buffer) {
    if (!get_theme_mod('enable_html_minification', false)) {
        return $buffer;
    }

    // Preserve <textarea>, <pre>, <script>, <style> content if we were doing aggressive HTML tag stripping
    // But for comments, we safely remove HTML comments first.
    
    // 1. Remove HTML comments
    $buffer = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $buffer);

    // 2. Remove CSS/JS comments
    // This is tricky as we don't want to remove URL protocols like https://
    // Regex to match multi-line comments /* ... */
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    
    // 3. Compact whitespace
    // Replace sequences of spaces/tabs with single space, but preserve newlines
    $buffer = preg_replace('/[ \t]+/', ' ', $buffer);
    
    // Remove spaces immediately between tags (where it's safe)
    $buffer = preg_replace('/>\s+</', '><', $buffer);

    return $buffer;
}

function chickensoft_blog_start_html_minification() {
    if (get_theme_mod('enable_html_minification', false) && !is_admin()) {
        ob_start('chickensoft_blog_minify_html');
    }
}
add_action('get_header', 'chickensoft_blog_start_html_minification');

// 9. Asynchronous Comment Email Notification (WP-Cron)
function chickensoft_disable_comment_notifications($post_id, $comment) {
    // Disable native synchronous notifications
    add_filter('notify_post_author', '__return_false');
    add_filter('notify_moderator', '__return_false');

    // Schedule asynchronous notification
    if (!wp_next_scheduled('chickensoft_send_comment_email', array($comment->comment_ID))) {
        wp_schedule_single_event(time(), 'chickensoft_send_comment_email', array($comment->comment_ID));
    }
}
// Hook into comment_post, which runs after comment is saved but before redirect.
// We use priority 5 to run before other plugins that might send emails, but typically core notifications are hardcoded.
// Actually, core calls wp_notify_postauthor inside wp_new_comment function.
// wp_new_comment triggers 'comment_post' action at the end.
// AND it triggers notifications BEFORE 'comment_post'.
// PROBLEM: By the time 'comment_post' fires, wp_new_comment has already called wp_notify_postauthor.

// Solution: We need to filter 'notify_post_author' and 'notify_moderator' GLOBALLY to false,
// but only during the initial request, NOT during our cron job.

// Step 1: Disable notifications on the front-end comment submission.
function chickensoft_defer_comment_emails($maybe_notify, $comment_id) {
    if (doing_action('chickensoft_send_comment_email')) {
        return $maybe_notify; // Allow if running in our cron job
    }
    
    // Check if we already scheduled the event to avoid duplicates
    if (!wp_next_scheduled('chickensoft_send_comment_email', array($comment_id))) {
        wp_schedule_single_event(time(), 'chickensoft_send_comment_email', array($comment_id));
    }
    
    return false; // Disable synchronous sending
}
add_filter('notify_post_author', 'chickensoft_defer_comment_emails', 10, 2);
add_filter('notify_moderator', 'chickensoft_defer_comment_emails', 10, 2);

// Step 2: Define the Cron Job Action
function chickensoft_process_async_comment_email($comment_id) {
    $comment = get_comment($comment_id);
    if (!$comment) return;

    // Remove our deferral filters to allow notifications to proceed
    remove_filter('notify_post_author', 'chickensoft_defer_comment_emails', 10);
    remove_filter('notify_moderator', 'chickensoft_defer_comment_emails', 10);

    // Manually trigger the notifications
    // Note: We need to check if the notification SHOULD be sent based on settings.
    // wp_notify_postauthor() and wp_notify_moderator() handle these checks internally (e.g. get_option('comments_notify')).
    
    // However, we need to know WHICH one to call. 
    // Usually, WordPress calls:
    // - wp_notify_moderator if comment_approved == '0' (held for moderation)
    // - wp_notify_postauthor if comment_approved == '1' (approved)
    
    if ($comment->comment_approved == '1') {
        wp_notify_postauthor($comment_id);
    } elseif ($comment->comment_approved == '0') {
        wp_notify_moderator($comment_id);
    }
}
add_action('chickensoft_send_comment_email', 'chickensoft_process_async_comment_email');
