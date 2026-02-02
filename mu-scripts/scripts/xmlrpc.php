<?php
/**
 * Deactivate XML-RPC in WordPress
 *
 * This file completely disables XML-RPC functionality:
 * - Blocks direct access to xmlrpc.php with 403 Forbidden
 * - Disables all XML-RPC methods
 * - Removes discovery headers and endpoints
 *
 * Note: X-Pingback headers are removed in pingback.php
 */

// 1.- Block direct access to xmlrpc.php with 403
add_action('init', function () {
    if (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) {
        http_response_code(403);
        exit('XML-RPC is disabled.');
    }
}, 1);

// 2.- Disable xml-rpc login endpoints
// see: https://developer.wordpress.org/reference/hooks/xmlrpc_enabled/
add_filter('xmlrpc_enabled', '__return_false');

// 3.- Disable all xml-rpc methods
add_filter('xmlrpc_methods', '__return_empty_array', PHP_INT_MAX);

// 4.- Remove XML-RPC Link header from HTTP response
add_filter('wp_headers', function ($headers) {
    unset($headers['X-Pingback']);
    if (isset($headers['Link'])) {
        $headers['Link'] = preg_replace('/<[^>]*xmlrpc[^>]*>;\s*rel="[^"]*",?\s*/i', '', $headers['Link']);
        if (empty(trim($headers['Link'], ', '))) {
            unset($headers['Link']);
        }
    }
    return $headers;
});

// 5.- Remove RSD (Really Simple Discovery) endpoint
remove_action('wp_head', 'rsd_link');

// 6.- Remove wlwmanifest (Windows Live Writer) link
remove_action('wp_head', 'wlwmanifest_link');

// 7.- Remove xmlrpc.php from robots.txt allow list
add_filter('xmlrpc_pingback_enabled', '__return_false');
