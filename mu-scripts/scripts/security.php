<?php
/**
 * Remove public wp rest api
 */
add_filter( 'rest_authentication_errors', function( $result ) {
    if ( ! empty( $result ) ) {
        return $result;
    }
    if ( ! is_user_logged_in() ) {
        return new WP_Error( 'rest_not_logged_in', 'You are not currently logged in.', array( 'status' => 401 ) );
    }
    return $result;
});

/**
 * Deactivate XML-RPC in WordPress
 * Contrary to the way it’s named, this filter does not control whether XML-RPC is fully enabled, rather,
 * it only controls whether XML-RPC methods requiring authentication – such as for publishing purposes – are enabled.
 * see: https://developer.wordpress.org/reference/hooks/xmlrpc_enabled/
 * then we need the step 2 to fully disable XML-RPC
 */
// 1.- Disable xml-rpc login endpoints
add_filter('xmlrpc_enabled', '__return_false');

// 2.- Disable all xml-rpc endpoints
add_filter('xmlrpc_methods', function () {
    return [];
}, PHP_INT_MAX);

/**
 * Disable X-Pingback headers
 *
 */
add_filter( 'wp_headers', function ( $headers ) {
    unset( $headers['X-Pingback'] );
    return $headers;
} );

add_filter('pings_open', '__return_false', PHP_INT_MAX);

add_action('init', function () {
    header_remove('X-Powered-By');
    header_remove('X-Pingback');
}, 1);

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

/**
 * Remove js and css versions to prevent wp version detected
 *
 */
add_filter( 'style_loader_src', function ( $src ) {
    if ( strpos( $src, '?ver=' ) ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}, 999 );

add_filter( 'script_loader_src', function ( $src ) {
    if ( strpos( $src, '?ver=' ) ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}, 999 );
