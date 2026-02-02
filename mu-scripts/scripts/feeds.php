<?php
/**
 * Disable RSS/Atom feeds completely
 *
 * This prevents content scraping and reduces server load.
 * All feed requests redirect to homepage with 301.
 */

add_action('after_setup_theme', function () {
    // Remove feed links from <head>
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);

    // Disable all feed types with 301 redirect
    $feed_actions = [
        'do_feed',
        'do_feed_rdf',
        'do_feed_rss',
        'do_feed_rss2',
        'do_feed_atom',
        'do_feed_rss2_comments',
        'do_feed_atom_comments',
    ];

    foreach ($feed_actions as $action) {
        add_action($action, function () {
            wp_safe_redirect(home_url(), 301);
            exit;
        }, 1);
    }
});

// Remove feed rewrite rules
add_filter('rewrite_rules_array', function ($rules) {
    foreach ($rules as $rule => $rewrite) {
        if (preg_match('/feed|rss|rdf|atom/i', $rule)) {
            unset($rules[$rule]);
        }
    }
    return $rules;
});
