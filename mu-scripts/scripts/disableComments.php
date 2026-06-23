<?php
/**
 * Disable Comments completely.
 *
 * Belt-and-braces lockdown on top of closing `comment_status` per-post:
 * - Forces `comments_open()` and `pings_open()` to false globally.
 * - Strips `comments` and `trackbacks` support from every post type so the
 *   editor metabox does not render and admins cannot re-open comments by
 *   mistake.
 * - Hides the Comments admin menu, the admin bar node and the dashboard
 *   "Recent comments" widget.
 * - Redirects direct visits to `edit-comments.php` / `options-discussion.php`.
 * - Returns 403 on submissions to `wp-comments-post.php`.
 * - Removes the `/wp/v2/comments` REST endpoints so authenticated users
 *   cannot create/edit/delete comments via the API either.
 *
 * Configuration via .env:
 * - WP_DISABLE_COMMENTS: true (default) | false to disable this script.
 *   Per-post `comment_status` still applies even when this script is off.
 */

if (!dotEnvReader('WP_DISABLE_COMMENTS', true)) {
    return;
}

// 1) Force every comments_open() / pings_open() check to return false.
add_filter('comments_open', '__return_false', PHP_INT_MAX);
add_filter('pings_open', '__return_false', PHP_INT_MAX);

// 2) Strip comment / trackback support from every registered post type.
add_action('init', function () {
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
        }
        if (post_type_supports($post_type, 'trackbacks')) {
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}, PHP_INT_MAX);

// 3) Hide the Comments menu and the Discussion settings page.
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
    remove_submenu_page('options-general.php', 'options-discussion.php');
}, PHP_INT_MAX);

// 4) Redirect any direct visit to the comments / discussion screens.
add_action('admin_init', function () {
    $pagenow = $GLOBALS['pagenow'] ?? '';

    if (in_array($pagenow, ['edit-comments.php', 'options-discussion.php'], true)) {
        wp_safe_redirect(admin_url());
        exit;
    }
});

// 5) Drop the Comments node from the admin bar.
add_action('admin_bar_menu', function ($bar) {
    if ($bar instanceof WP_Admin_Bar) {
        $bar->remove_node('comments');
    }
}, PHP_INT_MAX);

// 6) Remove the dashboard "Recent comments" widget.
add_action('wp_dashboard_setup', function () {
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
});

// 7) Block the public comment submission endpoint (wp-comments-post.php).
add_action('pre_comment_on_post', function () {
    wp_die(__('Los comentarios están deshabilitados.'), 403);
}, 1);

// 8) Remove the REST API comments endpoints so even authenticated users
//    cannot create/edit/delete comments via the API.
add_filter('rest_endpoints', function ($endpoints) {
    foreach ($endpoints as $route => $data) {
        if (strpos($route, '/wp/v2/comments') === 0) {
            unset($endpoints[$route]);
        }
    }
    return $endpoints;
});
