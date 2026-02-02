<?php
/**
 * SVG Upload Support
 *
 * Configuration via .env:
 * - WP_ALLOW_SVG not defined = SVG uploads NOT allowed
 * - WP_ALLOW_SVG: false = SVG uploads NOT allowed
 * - WP_ALLOW_SVG: true = SVG uploads allowed for all users
 * - WP_ALLOW_SVG: 'admin' = SVG uploads allowed only for administrators
 */

$allowSvg = dotEnvReader('WP_ALLOW_SVG', false);

if (!$allowSvg) {
    return;
}

add_filter('upload_mimes', function ($mimes) use ($allowSvg) {
    if ($allowSvg === 'admin' && !current_user_can('administrator')) {
        return $mimes;
    }
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
});

add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) use ($allowSvg) {
    if ($allowSvg === 'admin' && !current_user_can('administrator')) {
        return $data;
    }

    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if ($ext === 'svg' || $ext === 'svgz') {
        $data['type'] = 'image/svg+xml';
        $data['ext'] = $ext;
        $data['proper_filename'] = $filename;
    }
    return $data;
}, 10, 4);
