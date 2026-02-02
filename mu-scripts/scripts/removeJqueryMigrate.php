<?php
/**
 * Remove jQuery Migrate in JS Console Log
 * Public views only, not admin
 *
 * Configuration via .env:
 * - WP_JQUERY_MIGRATE not defined = jQuery Migrate removed (default)
 * - WP_JQUERY_MIGRATE: false = jQuery Migrate removed
 * - WP_JQUERY_MIGRATE: true = jQuery Migrate kept (for legacy compatibility)
 */

$keepJqueryMigrate = dotEnvReader('WP_JQUERY_MIGRATE', false);

if ($keepJqueryMigrate) {
    return;
}

add_action('wp_default_scripts', function ($scripts) {
    if (!empty($scripts->registered['jquery']) && !is_admin()) {
        $jquery_dependencies = $scripts->registered['jquery']->deps;
        $scripts->registered['jquery']->deps = array_diff($jquery_dependencies, ['jquery-migrate']);
    }
});
