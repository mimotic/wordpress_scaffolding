<?php
/**
 * Disable feed, rss and comments feed
 *
 */
function disable_feeds() {
    wp_die('No feed available, please visit our <a href="'. get_bloginfo('url') .'">homepage</a>!');
}

function remove_feeds() {
    remove_action('wp_head', 'feed_links_extra', 3); // Remove links to the extra feeds such as category feeds
    remove_action('wp_head', 'feed_links', 2); // Remove links to the general feeds: Post and Comment Feed
    add_action('do_feed', 'disable_feeds', 1);
    add_action('do_feed_rdf', 'disable_feeds', 1);
    add_action('do_feed_rss', 'disable_feeds', 1);
    add_action('do_feed_rss2', 'disable_feeds', 1);
    add_action('do_feed_atom', 'disable_feeds', 1);
}

add_action('after_setup_theme', 'remove_feeds');

/**
 * Disable Comments feed
 *
 */
add_action('do_feed_comments', function () {
    wp_die(__('Comments are closed.'), '', array('response' => 403));
}, 1);
add_action('do_feed_rss2_comments', function () {
    wp_die(__('Comments are closed.'), '', array('response' => 403));
}, 1);
