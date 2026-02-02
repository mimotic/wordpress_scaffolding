<?php
/**
 * Remove js and css versions to prevent WP version detection
 *
 * Configuration via .env:
 * - WP_SHOW_ASSET_VERSIONS not defined = versions removed (default)
 * - WP_SHOW_ASSET_VERSIONS: false = versions removed
 * - WP_SHOW_ASSET_VERSIONS: true = versions kept (for cache busting)
 */

$showAssetVersions = dotEnvReader('WP_SHOW_ASSET_VERSIONS', false);

if ($showAssetVersions) {
    return;
}

add_filter('style_loader_src', function ($src) {
    if (strpos($src, '?ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}, 999);

add_filter('script_loader_src', function ($src) {
    if (strpos($src, '?ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}, 999);
