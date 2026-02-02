<?php
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

// Note: X-Pingback headers are removed in pingback.php
