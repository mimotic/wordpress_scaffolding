<?php
/**
 * HTTP Security Headers
 *
 * Adds security headers to all responses to protect against common attacks.
 * These headers are recommended by OWASP and security scanners.
 */

add_action('send_headers', function () {
    // Prevent clickjacking attacks
    header('X-Frame-Options: SAMEORIGIN');

    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');

    // XSS Protection (legacy, but still useful for older browsers)
    header('X-XSS-Protection: 1; mode=block');

    // Control referrer information sent with requests
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Restrict browser features and APIs
    header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=()');

    // Prevent loading site in other site's iframes (additional protection)
    header('Content-Security-Policy: frame-ancestors \'self\'');

    // Only send cookies over HTTPS (if on HTTPS)
    if (is_ssl()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
});

// Remove PHP version from headers
add_action('init', function () {
    header_remove('X-Powered-By');
}, 1);

// Remove unnecessary WordPress headers
add_filter('wp_headers', function ($headers) {
    // Remove WordPress version hint
    unset($headers['X-Powered-By']);
    return $headers;
});
