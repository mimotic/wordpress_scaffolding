<?php
/**
 * REST API Security Configuration
 *
 * Security levels (configured via .env):
 * - WP_REST_ALLOW_USERS: false (default) = block user enumeration endpoints ALWAYS
 * - WP_REST_PUBLIC_ACCESS: false (default) = block all public API access except whitelist
 *
 * By default, the most restrictive configuration is applied.
 */

// Get configuration from .env (defaults to most restrictive)
$restAllowUsers = dotEnvReader('WP_REST_ALLOW_USERS', false);
$restPublicAccess = dotEnvReader('WP_REST_PUBLIC_ACCESS', false);

/**
 * LEVEL 1 - ALWAYS ACTIVE: Block user enumeration endpoints
 * This prevents username discovery via REST API
 * Blocked endpoints: /wp/v2/users, /wp/v2/users/*, search by user type
 */
if (!$restAllowUsers) {
    // Remove users endpoint completely from REST API
    add_filter('rest_endpoints', function ($endpoints) {
        // Remove all user-related endpoints
        foreach ($endpoints as $route => $data) {
            if (preg_match('/\/wp\/v2\/users/', $route)) {
                unset($endpoints[$route]);
            }
        }
        return $endpoints;
    });

    // Block user enumeration via author archives (?author=1)
    add_action('template_redirect', function () {
        if (is_author() && !is_user_logged_in()) {
            wp_safe_redirect(home_url(), 301);
            exit;
        }
    });

    // Remove author name from oEmbed responses
    add_filter('oembed_response_data', function ($data) {
        unset($data['author_name']);
        unset($data['author_url']);
        return $data;
    });

    // Remove author info from post REST responses for non-logged users
    add_filter('rest_prepare_post', function ($response, $post, $request) {
        if (!is_user_logged_in()) {
            $data = $response->get_data();
            unset($data['author']);
            $response->set_data($data);
        }
        return $response;
    }, 10, 3);
}

/**
 * LEVEL 2: Block all public REST API access
 * Only whitelisted routes are accessible without authentication
 */
if (!$restPublicAccess) {
    add_filter('rest_authentication_errors', function ($result) {
        // Don't override existing errors
        if (!empty($result)) {
            return $result;
        }

        // Always allow logged-in users
        if (is_user_logged_in()) {
            return $result;
        }

        // Whitelist: routes that work without authentication
        $allowed_routes = [
            '/wp-json/contact-form-7',      // Contact Form 7
            '/wp-json/wp/v2/contact-form-7', // CF7 alternative
            '/wp-json/mcp/',                 // Custom MCP endpoints
            // Add more routes as needed
        ];

        $url = $_SERVER['REQUEST_URI'] ?? '';
        $path = parse_url($url, PHP_URL_PATH);

        foreach ($allowed_routes as $route) {
            if (strpos($path, $route) !== false) {
                return $result;
            }
        }

        return new WP_Error(
            'rest_not_logged_in',
            __('REST API access restricted.'),
            ['status' => 401]
        );
    });
}
