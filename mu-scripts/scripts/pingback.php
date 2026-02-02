<?php
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
