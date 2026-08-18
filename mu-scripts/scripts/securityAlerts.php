<?php
/**
 * Security Alerts — early-warning / intrusion detection
 *
 * Lightweight, dependency-free monitoring that emails (and logs) the moment a
 * high-signal security event happens, so a compromise is caught on day 1 rather
 * than discovered weeks later. No plugin dependencies: every alert is written to
 * the PHP error log and (throttled) emailed via wp_mail.
 *
 * What it watches (all high-signal, low-noise):
 *  1. A user becomes administrator (created as admin, promoted, or multisite
 *     super-admin). This is the #1 backdoor signature — rogue admin accounts.
 *  2. A plugin is activated or the theme is switched.
 *  3. A critical option changes: siteurl, home, admin_email, users_can_register,
 *     default_role, template/stylesheet.
 *  4. A PHP-executable file is uploaded through the media handler — BLOCKED and
 *     reported (uploads must never contain executable code).
 *  5. A daily cron scan finds executable PHP under wp-content/uploads.
 *  6. A brute-force spike (many failed logins in a short window).
 *
 * Configuration via .env:
 *  - WP_SECURITY_ALERTS:        true (default) | false to disable everything.
 *  - WP_SECURITY_ALERT_EMAIL:   recipient (default: the admin_email option).
 *  - WP_SECURITY_ALERT_THROTTLE: minutes to de-duplicate identical alerts (default 15).
 *  - WP_SECURITY_BRUTEFORCE_THRESHOLD: failed logins per 5 min before alerting (default 20).
 *  - WP_SECURITY_BLOCK_PHP_UPLOAD: true (default) | false to alert-only without blocking.
 *
 * Safe by design: wrapped in existence checks, never throws, never blocks a
 * legitimate request. If mail delivery fails the event is still logged.
 */

if (!dotEnvReader('WP_SECURITY_ALERTS', true)) {
    return;
}

if (!defined('MIMOTIC_SEC_THROTTLE_MIN')) {
    define('MIMOTIC_SEC_THROTTLE_MIN', max(1, (int) dotEnvReader('WP_SECURITY_ALERT_THROTTLE', 15)));
}

/**
 * Resolve the requesting client IP (proxy/Forge aware).
 */
if (!function_exists('mimotic_sec_client_ip')) {
    function mimotic_sec_client_ip(): string
    {
        foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $h) {
            if (empty($_SERVER[$h])) {
                continue;
            }
            $ip = trim(explode(',', (string) $_SERVER[$h])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
        return (defined('WP_CLI') && WP_CLI) ? 'wp-cli' : 'unknown';
    }
}

/**
 * Central dispatcher: log + (throttled) email.
 *
 * @param string $code    short machine code, e.g. 'admin_created'
 * @param string $subject human subject line
 * @param array  $context key => value details included in the body
 */
if (!function_exists('mimotic_sec_alert')) {
    function mimotic_sec_alert(string $code, string $subject, array $context = []): void
    {
        $ip    = mimotic_sec_client_ip();
        $user  = function_exists('wp_get_current_user') ? wp_get_current_user() : null;
        $who   = ($user && $user->ID) ? sprintf('%s (#%d)', $user->user_login, $user->ID) : 'no-auth/system';
        $site  = function_exists('home_url') ? home_url() : (dotEnvReader('SITE_URL', '') ?: '');
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? substr((string) $_SERVER['HTTP_USER_AGENT'], 0, 200) : '';
        $uri   = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';

        $lines = [
            "[ALERTA SEGURIDAD] $subject",
            'Sitio:    ' . $site,
            'Evento:   ' . $code,
            'Actor:    ' . $who,
            'IP:       ' . $ip,
            'URI:      ' . $uri,
            'UA:       ' . $agent,
            'Fecha:    ' . (function_exists('current_time') ? current_time('mysql') : ''),
        ];
        foreach ($context as $k => $v) {
            $lines[] = sprintf('%-9s %s', ucfirst($k) . ':', is_scalar($v) ? (string) $v : wp_json_encode($v));
        }
        $body = implode("\n", $lines);

        // 1) Always log to the PHP error log (WP_DEBUG_LOG path if defined).
        error_log('[securityAlerts] ' . str_replace("\n", ' | ', $body));

        // 2) Throttled email (de-dup identical code+subject within the window).
        if (function_exists('get_transient')) {
            $key = 'mimotic_sec_' . md5($code . '|' . $subject);
            if (get_transient($key)) {
                return;
            }
            set_transient($key, 1, MIMOTIC_SEC_THROTTLE_MIN * MINUTE_IN_SECONDS);
        }

        if (function_exists('wp_mail')) {
            $to = dotEnvReader('WP_SECURITY_ALERT_EMAIL', '') ?: get_option('admin_email');
            if ($to) {
                $host = parse_url($site, PHP_URL_HOST) ?: 'site';
                @wp_mail($to, "[$host] $subject", $body);
            }
        }
    }
}

/* ------------------------------------------------------------------ *
 * 1) Administrator created / promoted  (the key backdoor signature)
 * ------------------------------------------------------------------ */
$mimotic_sec_check_admin = function ($user_id, $roles_source = null) {
    $user = get_userdata($user_id);
    if (!$user) {
        return;
    }
    if (in_array('administrator', (array) $user->roles, true)) {
        mimotic_sec_alert(
            'admin_role_granted',
            'Cuenta con rol ADMINISTRATOR: ' . $user->user_login,
            ['user' => $user->user_login, 'email' => $user->user_email, 'user_id' => $user_id]
        );
    }
};

add_action('set_user_role', function ($user_id, $role) use ($mimotic_sec_check_admin) {
    if ('administrator' === $role) {
        $mimotic_sec_check_admin($user_id);
    }
}, 10, 2);

add_action('add_user_role', function ($user_id, $role) use ($mimotic_sec_check_admin) {
    if ('administrator' === $role) {
        $mimotic_sec_check_admin($user_id);
    }
}, 10, 2);

add_action('user_register', function ($user_id) use ($mimotic_sec_check_admin) {
    $mimotic_sec_check_admin($user_id);
}, 10, 1);

add_action('profile_update', function ($user_id, $old = null) use ($mimotic_sec_check_admin) {
    $mimotic_sec_check_admin($user_id);
}, 10, 2);

add_action('granted_super_admin', function ($user_id) {
    $u = get_userdata($user_id);
    mimotic_sec_alert('super_admin_granted', 'Super Admin concedido: ' . ($u ? $u->user_login : $user_id), ['user_id' => $user_id]);
}, 10, 1);

/* ------------------------------------------------------------------ *
 * 2) Plugin activation / theme switch
 * ------------------------------------------------------------------ */
add_action('activated_plugin', function ($plugin) {
    mimotic_sec_alert('plugin_activated', 'Plugin ACTIVADO: ' . $plugin, ['plugin' => $plugin]);
}, 10, 1);

add_action('switch_theme', function ($new_name) {
    mimotic_sec_alert('theme_switched', 'Tema CAMBIADO a: ' . $new_name, ['theme' => $new_name]);
}, 10, 1);

/* ------------------------------------------------------------------ *
 * 3) Critical option changes
 * ------------------------------------------------------------------ */
foreach (['siteurl', 'home', 'admin_email', 'users_can_register', 'default_role', 'template', 'stylesheet'] as $mimotic_opt) {
    add_action("update_option_{$mimotic_opt}", function ($old, $new, $option) {
        if ($old === $new) {
            return;
        }
        mimotic_sec_alert(
            'option_changed',
            'Opción crítica modificada: ' . $option,
            ['option' => $option, 'old' => $old, 'new' => $new]
        );
    }, 10, 3);
}

/* ------------------------------------------------------------------ *
 * 4) Block + alert on executable uploads through the media handler
 * ------------------------------------------------------------------ */
add_filter('wp_handle_upload_prefilter', function ($file) {
    $name = isset($file['name']) ? strtolower($file['name']) : '';
    $bad  = '/\.(php\d?|phtml|phar|pht|phps|cgi|pl|py|sh|asp|aspx|jsp)($|\.)/i';
    // Also catch double extensions like image.jpg.php
    if ($name && (preg_match($bad, $name) || preg_match('/\.(php\d?|phtml|phar)\b/i', $name))) {
        mimotic_sec_alert('exec_upload_blocked', 'Intento de subir fichero ejecutable: ' . $file['name'], ['file' => $file['name']]);
        if (dotEnvReader('WP_SECURITY_BLOCK_PHP_UPLOAD', true)) {
            $file['error'] = __('Tipo de archivo no permitido.');
        }
    }
    return $file;
}, 1);

/* ------------------------------------------------------------------ *
 * 5) Daily cron scan of uploads for executable PHP
 * ------------------------------------------------------------------ */
add_action('mimotic_sec_scan_uploads', function () {
    if (!function_exists('wp_upload_dir')) {
        return;
    }
    $base = wp_upload_dir();
    $dir  = isset($base['basedir']) ? $base['basedir'] : '';
    if (!$dir || !is_dir($dir)) {
        return;
    }
    $hits = [];
    try {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
        foreach ($it as $f) {
            $fn = strtolower($f->getFilename());
            // index.php guards ("Silence is golden") are legitimate; ignore them.
            if ($fn === 'index.php') {
                continue;
            }
            if (preg_match('/\.(php\d?|phtml|phar|pht|phps)$/i', $fn)) {
                // Only flag files that actually contain PHP code. Empty stubs
                // (e.g. an empty wpallimport/functions.php) and 0-byte files
                // carry no executable payload, so they are not a threat.
                if ($f->getSize() === 0) {
                    continue;
                }
                $head = @file_get_contents($f->getPathname(), false, null, 0, 8192);
                if ($head === false || strpos($head, '<?') === false) {
                    continue;
                }
                $hits[] = str_replace($dir, '', $f->getPathname());
                if (count($hits) >= 25) {
                    break;
                }
            }
        }
    } catch (\Throwable $e) {
        return;
    }
    if ($hits) {
        mimotic_sec_alert(
            'php_in_uploads',
            'PHP EJECUTABLE detectado en uploads (' . count($hits) . ')',
            ['files' => $hits]
        );
    }
});

add_action('init', function () {
    if (function_exists('wp_next_scheduled') && !wp_next_scheduled('mimotic_sec_scan_uploads')) {
        wp_schedule_event(time() + 300, 'daily', 'mimotic_sec_scan_uploads');
    }
});

/* ------------------------------------------------------------------ *
 * 6) Brute-force spike detector (complements loginHardening's per-IP lockout)
 * ------------------------------------------------------------------ */
add_action('wp_login_failed', function () {
    if (!function_exists('get_transient')) {
        return;
    }
    $threshold = max(5, (int) dotEnvReader('WP_SECURITY_BRUTEFORCE_THRESHOLD', 20));
    $key = 'mimotic_sec_bf_count';
    $n = (int) get_transient($key) + 1;
    set_transient($key, $n, 5 * MINUTE_IN_SECONDS);
    if ($n === $threshold) {
        mimotic_sec_alert(
            'bruteforce_spike',
            'Posible ataque de fuerza bruta: ' . $n . ' fallos de login en 5 min',
            ['fails_5min' => $n]
        );
    }
}, 10, 0);
