<?php
/**
 * Disable WordPress Search
 *
 * The Solaria UI exposes no search by design. Killing search frontend-wide:
 *  - removes the search form rendered by themes/widgets (incl. the
 *    GeneratePress 404 template),
 *  - redirects any `?s=...` or `/search/...` request to the home page so
 *    bots scanning for search-based SQL/UX bugs hit a fast 301 instead of
 *    spinning up a real WP_Query,
 *  - replaces the "Maybe try searching?" message that GeneratePress prints
 *    on the 404 page with a neutral, per-language text.
 *
 * Configuration via .env:
 * - WP_DISABLE_SEARCH: true (default) | false to re-enable search.
 */

if (!dotEnvReader('WP_DISABLE_SEARCH', true)) {
    return;
}

// 1) Make get_search_form() return nothing wherever it is called.
add_filter('get_search_form', '__return_empty_string');

// 2) Catch /?s=... early (before parse_request) and 301-redirect to home.
//    Done in `init` so we never instantiate the search WP_Query for bots.
add_action('init', function () {
    if (is_admin() || (defined('DOING_AJAX') && DOING_AJAX) || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }

    if (isset($_GET['s']) || isset($_POST['s'])) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
});

// 3) Catch pretty /search/... URLs that don't carry ?s= but still resolve to
//    a search query via rewrite rules.
add_action('template_redirect', function () {
    if (!is_search()) {
        return;
    }

    wp_safe_redirect(home_url('/'), 301);
    exit;
});

// 4) Defense in depth: if anything slipped through, strip `s` from the
//    request so WP_Query never runs as a search.
add_action('parse_request', function ($wp) {
    if (!is_admin() && isset($wp->query_vars['s'])) {
        unset($wp->query_vars['s']);
    }
});

// 4) Replace the GeneratePress 404 invitation to search with a neutral
//    message per active site language.
add_filter('generate_404_text', function ($text) {
    $messages = [
        'es' => 'La página solicitada no existe o se ha movido.',
        'en' => 'The page you requested does not exist or has been moved.',
        'it' => 'La pagina richiesta non esiste o è stata spostata.',
        'pt' => 'A página solicitada não existe ou foi movida.',
        'de' => 'Die angeforderte Seite existiert nicht oder wurde verschoben.',
    ];

    $lang = function_exists('pll_current_language') ? pll_current_language('slug') : '';

    return $messages[$lang] ?? $messages['es'];
});
