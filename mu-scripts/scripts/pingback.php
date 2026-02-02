<?php
/**
 * Disable Pingback, Trackback and related features
 *
 * Pingbacks and trackbacks are legacy features that are commonly
 * exploited for DDoS amplification attacks.
 */

// Remove X-Pingback header
add_filter('wp_headers', function ($headers) {
    unset($headers['X-Pingback']);
    return $headers;
});

// Remove X-Powered-By header (PHP version disclosure)
add_action('init', function () {
    header_remove('X-Powered-By');
    header_remove('X-Pingback');
}, 1);

// Disable pings
add_filter('pings_open', '__return_false', PHP_INT_MAX);

// Disable trackbacks
add_filter('xmlrpc_allow_anonymous_comments', '__return_false');

// Remove trackback rewrite rules
add_filter('rewrite_rules_array', function ($rules) {
    foreach ($rules as $rule => $rewrite) {
        if (preg_match('/trackback/i', $rule)) {
            unset($rules[$rule]);
        }
    }
    return $rules;
});

// Disable self-pingbacks (pinging your own site)
add_action('pre_ping', function (&$links) {
    $home = home_url();
    foreach ($links as $key => $link) {
        if (strpos($link, $home) === 0) {
            unset($links[$key]);
        }
    }
});

// Remove pingback URL from page source
add_filter('bloginfo_url', function ($output, $show) {
    if ($show === 'pingback_url') {
        return '';
    }
    return $output;
}, 10, 2);
