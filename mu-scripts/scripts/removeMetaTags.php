<?php
/**
 * Remove version fingerprints and generator meta tags
 *
 * Hides CMS and plugin versions to prevent targeted attacks.
 * Covers WordPress core, popular plugins, themes and page builders.
 */

add_action('init', function () {
    // ===== WORDPRESS CORE =====
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_resource_hints', 2);
    remove_action('wp_head', 'wp_shortlink_wp_head', 10);
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');

    // ===== EMOJI (reduces page load) =====
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('tiny_mce_plugins', function ($plugins) {
        return is_array($plugins) ? array_diff($plugins, ['wpemoji']) : [];
    });
    add_filter('wp_resource_hints', function ($urls, $type) {
        if ($type === 'dns-prefetch') {
            $urls = array_filter($urls, function ($url) {
                return strpos($url, 'emoji') === false;
            });
        }
        return $urls;
    }, 10, 2);

    // ===== WPML =====
    global $sitepress;
    if ($sitepress) {
        remove_action('wp_head', [$sitepress, 'meta_generator_tag']);
    }
    add_filter('wpml_meta_generator_tag', '__return_empty_string');

    // ===== WOOCOMMERCE =====
    remove_action('wp_head', 'woo_version');
    add_filter('woocommerce_hide_invisible_variations', '__return_true');

    // ===== YOAST SEO =====
    add_filter('wpseo_debug_markers', '__return_false');
    add_filter('wpseo_remove_reply_to_com', '__return_true');

    // ===== RANK MATH =====
    remove_action('wp_head', 'rank_math_head');
    add_filter('rank_math/frontend/remove_credit_notice', '__return_true');

    // ===== JETPACK =====
    add_filter('jetpack_implode_frontend_css', '__return_false');
    add_filter('jetpack_open_graph_tags', '__return_empty_array');

    // ===== SLIDER REVOLUTION =====
    add_filter('revslider_meta_generator', '__return_empty_string');

    // ===== ELEMENTOR =====
    add_action('elementor/frontend/after_register_styles', function () {
        foreach (['elementor-icons', 'elementor-animations'] as $handle) {
            wp_deregister_style($handle);
        }
    }, 20);

    // ===== WPBAKERY / VISUAL COMPOSER =====
    add_filter('vc_front_css', '__return_empty_string');

    // ===== DIVI =====
    remove_action('wp_head', 'et_add_viewport_meta');

    // ===== BEAVER BUILDER =====
    add_filter('fl_builder_render_module_html', function ($html, $module) {
        return preg_replace('/<!--.*?-->/s', '', $html);
    }, 10, 2);

    // ===== GRAVITY FORMS =====
    add_filter('gform_display_product_summary', '__return_true');

    // ===== ACF =====
    add_filter('acf/settings/show_admin', '__return_true');

    // ===== CONTACT FORM 7 =====
    add_filter('wpcf7_load_js', '__return_true');
    add_filter('wpcf7_load_css', '__return_true');

    // ===== WORDPRESS REST API =====
    remove_action('template_redirect', 'rest_output_link_header', 11);

}, 1);

// Remove generator meta tag from RSS feeds
add_filter('the_generator', '__return_empty_string');

// Remove WordPress version from login page
add_filter('login_footer', function () {
    remove_filter('update_footer', 'core_update_footer');
});

// Remove version from admin footer
add_filter('admin_footer_text', '__return_empty_string');
add_filter('update_footer', '__return_empty_string', 11);
