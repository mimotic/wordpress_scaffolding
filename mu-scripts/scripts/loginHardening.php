<?php
/**
 * Login Hardening
 *
 * Self-hosted replacement for the limit-login-attempts-reloaded plugin plus
 * a handful of cheap login defences:
 *
 * - Hide which credential failed (username vs password) and disable user
 *   registration regardless of the option value.
 * - Reject obviously-bad usernames at the authenticate stage so brute-force
 *   bots never reach the DB lookup.
 * - Throttle wp-login.php and lostpassword by IP using WP transients.
 *
 * Configuration via .env:
 * - WP_LOGIN_HARDENING:        true (default) | false to disable everything
 * - WP_LOGIN_MAX_ATTEMPTS:     failed attempts allowed before lockout (default 5)
 * - WP_LOGIN_LOCKOUT_MINUTES:  lockout duration in minutes (default 30)
 * - WP_LOGIN_ATTEMPT_WINDOW:   rolling window in minutes (default 15)
 * - WP_LOGIN_BLOCKED_USERS:    comma-separated usernames rejected upfront
 *                              (default: common brute-force targets, empty to disable)
 *
 * Manual unlock:
 *   wp transient delete --all                       # nuke all login lockouts (also other transients)
 *   wp option get _transient_mimotic_login_lockout_<md5(ip)>   # to inspect a single lock
 */

if (!dotEnvReader('WP_LOGIN_HARDENING', true)) {
    return;
}

$mimoticLoginConfig = [
    'max_attempts'    => max(1, (int) dotEnvReader('WP_LOGIN_MAX_ATTEMPTS', 5)),
    'lockout_minutes' => max(1, (int) dotEnvReader('WP_LOGIN_LOCKOUT_MINUTES', 30)),
    'window_minutes'  => max(1, (int) dotEnvReader('WP_LOGIN_ATTEMPT_WINDOW', 15)),
    'blocked_users'   => array_filter(array_map(
        'strtolower',
        array_map('trim', explode(',', (string) dotEnvReader(
            'WP_LOGIN_BLOCKED_USERS',
            'admin,administrator,root,test,wp,wpadmin,user,demo,support'
        )))
    )),
];

/**
 * Resolve the requesting client IP, accounting for Forge / reverse proxies.
 * Falls back to a non-routable address so legitimate transients still work
 * when the IP cannot be determined.
 */
function mimotic_login_client_ip(): string
{
    foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $header) {
        if (empty($_SERVER[$header])) {
            continue;
        }

        $candidate = trim(explode(',', (string) $_SERVER[$header])[0]);

        if (filter_var($candidate, FILTER_VALIDATE_IP)) {
            return $candidate;
        }
    }

    return '0.0.0.0';
}

function mimotic_login_transient_key(string $type, string $key): string
{
    return 'mimotic_login_' . $type . '_' . md5($key);
}

/**
 * Disable in-WP user registration even if the option flips by mistake.
 */
add_filter('option_users_can_register', '__return_zero');
add_filter('pre_option_users_can_register', '__return_zero');

/**
 * Collapse login error messages so they no longer reveal which credential
 * was wrong, while keeping the lockout / blocked-user messages intact so the
 * user still gets the "try again in N minutes" feedback.
 *
 * `wp_login_errors` passes the WP_Error object (not the formatted string), so
 * we can inspect codes here. Anything that isn't one of our hardening codes
 * is rewritten to a single generic message.
 */
add_filter('wp_login_errors', function ($errors) {
    if (!is_wp_error($errors) || !$errors->has_errors()) {
        return $errors;
    }

    $preserved_codes = ['mimotic_login_locked', 'mimotic_blocked_user'];

    foreach ($errors->get_error_codes() as $code) {
        if (in_array($code, $preserved_codes, true)) {
            return $errors;
        }
    }

    return new WP_Error('mimotic_invalid_credentials', __('Credenciales no válidas.'));
}, 10, 1);

/**
 * Stage 1 + 2 — enforce blocked usernames AND IP lockout.
 *
 * Must run AFTER `wp_authenticate_username_password` (priority 20), because
 * that core callback ignores any incoming WP_Error and overwrites it with the
 * result of its own DB check. Hooking at priority 30 lets us replace whatever
 * core returned (WP_Error or WP_User) and guarantee the block sticks even
 * when the supplied password is correct.
 */
add_filter('authenticate', function ($user, $username) use ($mimoticLoginConfig) {
    if ('' === $username) {
        return $user;
    }

    if (
        !empty($mimoticLoginConfig['blocked_users'])
        && in_array(strtolower($username), $mimoticLoginConfig['blocked_users'], true)
    ) {
        return new WP_Error('mimotic_blocked_user', __('Credenciales no válidas.'));
    }

    if (get_transient(mimotic_login_transient_key('lockout', mimotic_login_client_ip()))) {
        return new WP_Error(
            'mimotic_login_locked',
            sprintf(
                __('Demasiados intentos fallidos. Inténtalo de nuevo en %d minutos.'),
                $mimoticLoginConfig['lockout_minutes']
            )
        );
    }

    return $user;
}, 30, 2);

/**
 * Stage 3 — count failed attempts and trigger lockouts.
 */
add_action('wp_login_failed', function ($username) use ($mimoticLoginConfig) {
    $ip          = mimotic_login_client_ip();
    $counter_key = mimotic_login_transient_key('count', $ip);
    $lockout_key = mimotic_login_transient_key('lockout', $ip);

    $attempts = (int) get_transient($counter_key);
    $attempts++;

    if ($attempts >= $mimoticLoginConfig['max_attempts']) {
        set_transient($lockout_key, time(), $mimoticLoginConfig['lockout_minutes'] * MINUTE_IN_SECONDS);
        delete_transient($counter_key);

        error_log(sprintf(
            '[loginHardening] IP %s locked for %d min after %d failed attempts (last attempted user: %s)',
            $ip,
            $mimoticLoginConfig['lockout_minutes'],
            $attempts,
            is_string($username) ? $username : '?'
        ));

        return;
    }

    set_transient($counter_key, $attempts, $mimoticLoginConfig['window_minutes'] * MINUTE_IN_SECONDS);
});

/**
 * Stage 4 — reset the failure counter on every successful login.
 */
add_action('wp_login', function () {
    delete_transient(mimotic_login_transient_key('count', mimotic_login_client_ip()));
});

/**
 * Stage 5 — apply the same throttle to lost-password requests so the form is
 * not abused for email enumeration / spamming the SMTP queue.
 */
add_action('lostpassword_post', function ($errors) use ($mimoticLoginConfig) {
    if (!is_wp_error($errors)) {
        return;
    }

    $ip          = mimotic_login_client_ip();
    $counter_key = mimotic_login_transient_key('lostpw', $ip);
    $lockout_key = mimotic_login_transient_key('lockout', $ip);

    if (get_transient($lockout_key)) {
        $errors->add('mimotic_login_locked', __('Demasiados intentos. Vuelve más tarde.'));
        return;
    }

    $attempts = (int) get_transient($counter_key);
    $attempts++;

    if ($attempts >= $mimoticLoginConfig['max_attempts']) {
        set_transient($lockout_key, time(), $mimoticLoginConfig['lockout_minutes'] * MINUTE_IN_SECONDS);
        delete_transient($counter_key);
        $errors->add('mimotic_login_locked', __('Demasiados intentos. Vuelve más tarde.'));
        return;
    }

    set_transient($counter_key, $attempts, $mimoticLoginConfig['window_minutes'] * MINUTE_IN_SECONDS);
});
