<?php
/**
 * Mimotic  enviroment.
 * DO NOT MODIFY !!!
 * @package mimotic.com
 */

require_once __DIR__ . '/dotEnvReader.php';



/**
 * Configuración básica de WordPress.
 *
 * Este archivo contiene las siguientes configuraciones: ajustes de MySQL,
 * prefijo de tablas,
 * claves secretas, idioma de WordPress y ABSPATH. Para obtener más información,
 * visita la página del Codex
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} .
 * Los ajustes de MySQL te los proporcionará tu proveedor de alojamiento web.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */


// ** Ajustes de MySQL. Solicita estos datos a tu proveedor de alojamiento web. ** //
/**
 * The name of the database for WordPress
 */
define('DB_NAME', dotEnvReader('DB_NAME', 'mimoticdb'));

/**
 * Database username
 */
define('DB_USER', dotEnvReader('DB_USER', 'root'));

/**
 * Database password
 */
define('DB_PASSWORD', dotEnvReader('DB_PASSWORD', 'root'));

/**
 * Hostname
 */
define('DB_HOST', dotEnvReader('DB_HOST', 'www'));


/**
 * Codificación de caracteres para la base de datos.
 */
define('DB_CHARSET', 'utf8');


/**
 * l¡Lang default.
 */
define('WPLANG', 'es_ES');



/**
 * Claves únicas de autentificación.
 *
 * Define cada clave secreta con una frase aleatoria distinta.
 * Puedes generarlas usando el {@link https://api.wordpress.org/secret-key/1.1/salt/
 * servicio de claves secretas de WordPress}
 * Puedes cambiar las claves en cualquier momento para invalidar
 * todas las cookies existentes.
 * Esto forzará a todos los usuarios a volver a hacer login.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'a&XliJ$K;tze08Y~zGxGw#e- ?jyocb<c]xU{CRq.O1$?h/-1NE^$tE^Wg<eP]2+');
define('SECURE_AUTH_KEY',  '{ycJ~rPw>Hzt/(/T_{n9ZPs/Nv-Jd_kVcQ*N(+;qHu;qO/L;JW$W@k_]%uRS4;Gi');
define('LOGGED_IN_KEY',    'y)K{^0dkZCOMfhFT/rU:@yz>Cp^yOb_%GgSW>1c(OAyVo94M&;s|<YuAgja<;(~m');
define('NONCE_KEY',        'm~tu.v_Z=ll$F>8R%<_^SB!< f1vGb/D)`{0/_cO|:cl -Ap{ysd9i@]$68jD!~Z');
define('AUTH_SALT',        'EIs+/l1.bJGsu;:2YJq-#V? 5;VE$5orn?}l=<@:+a5Jn!LQN{L<-}f:4x^S6y&H');
define('SECURE_AUTH_SALT', 'n7m+UN{<dtMDMr%WSz^l:@s?`)R97|L-`MMs@`:i2soOqBb]oFKB6^.AwBA8Ab(Q');
define('LOGGED_IN_SALT',   'kwbSY>?U9Ks*Lc/%3sV{2U-?zhBmG@DHW` -lzYO-=9_:;cn`<.[.)~)U%w/`I}L');
define('NONCE_SALT',       '4pE0$>2[/&@/nsk3>whE?TGk9g^_d[ >817f(Jm9[KtZW nQ#C0gNx~+ehvd5}.E');



/**
 * Prefijo de la base de datos de WordPress.
 *
 * Cambia el prefijo si deseas instalar multiples blogs en una sola base de datos.
 * Emplea solo números, letras y guión bajo.
 */
$table_prefix = dotEnvReader('TABLE_PREFIX', 'mimo_');
define('WP_HOME', dotEnvReader('SITE_URL', 'https://mimotic.com/'));
define('WP_SITEURL', dotEnvReader('SITE_URL', 'https://mimotic.com/'));


/**
 * Entornos de desarrollo
 */
if (dotEnvReader('SITE_ENVIROMENT') === 'production') { // production server

    //ssl
    define('FORCE_SSL_ADMIN', true);
    define('FORCE_SSL_LOGIN', true);

    // debug
    define('WP_DEBUG', true);
    define('SCRIPT_DEBUG', false);
    @ini_set('display_errors', 0);
    define( 'WP_DEBUG_LOG', __DIR__ . '/logs/wp-production-debug.log' );
    define('WP_DEBUG_DISPLAY', false);
    define('SAVEQUERIES', false);
    if (!defined('WP_CACHE')) {
        define( 'WP_CACHE', true );
    }

} else if (dotEnvReader('SITE_ENVIROMENT') === 'stagin') {

    //ssl
    define('FORCE_SSL_ADMIN', true);
    define('FORCE_SSL_LOGIN', true);

    // debug
    define('WP_DEBUG', true);
    define('SCRIPT_DEBUG', true);
    define( 'WP_DEBUG_LOG', __DIR__ . '/logs/wp-staging-debug.log' );
    define('WP_DEBUG_DISPLAY', true);
    define('SAVEQUERIES', true);


} else { // local or lax servers

    // ssl
    define('FORCE_SSL_ADMIN', false);
    define('FORCE_SSL_LOGIN', false);

    // debug
    define('WP_DEBUG', true);
    define('SCRIPT_DEBUG', true);
    define( 'WP_DEBUG_LOG', __DIR__ . '/logs/wp-local-debug.log' );
    define('WP_DEBUG_DISPLAY', true);
    define('SAVEQUERIES', true);
}


/**
 * Allow WordPress to detect HTTPS when used behind a reverse proxy or a load balancer
 * See https://codex.wordpress.org/Function_Reference/is_ssl#Notes
 */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}


/*
|--------------------------------------------------------------------------
| Add-on: SendGrid
|--------------------------------------------------------------------------
|
| By default, WordPress uses PHP's mail function to send emails. We
| strongly recommend using SendGrid to ensure messages are delivered to
| both you and your users.
|
*/

//define('SENDGRID_USERNAME', '' );
//define('SENDGRID_PASSWORD', '');
//define('SENDGRID_SEND_METHOD', 'api');

//
//define('SENDGRID_FROM_NAME', '');
//define('SENDGRID_FROM_EMAIL', '');
//define('SENDGRID_REPLY_TO', '');
//define('SENDGRID_CATEGORIES', '');


/*
|--------------------------------------------------------------------------
| AWS media deslocalizada S3 -  4 mimotic
|--------------------------------------------------------------------------
|
|
*/

//define( 'AS3CF_SETTINGS', serialize( array(
//    'provider' => 'aws',
//    'access-key-id' => dotEnvReader('AWS_ACCESS_KEY_ID', '*********'),
//    'secret-access-key' => dotEnvReader('AWS_SECRET_ACCESS_KEY', '*********'),
//) ) );


/*
|--------------------------------------------------------------------------
| PERFORMANCE
|--------------------------------------------------------------------------
|
|
*/

define('DISABLE_WP_CRON', true);
define('EMPTY_TRASH_DAYS', 0);
define('WP_POST_REVISIONS', false);
//define('WP_MEMORY_LIMIT', '256M');

/*

|--------------------------------------------------------------------------
| MULTISITE
|--------------------------------------------------------------------------
|
|
*/


//define('WP_ALLOW_MULTISITE', dotEnvReader('WP_ALLOW_MULTISITE', false));
//define('MULTISITE', dotEnvReader('MULTISITE', false));
//define('SUBDOMAIN_INSTALL', dotEnvReader('SUBDOMAIN_INSTALL', false));
//define('DOMAIN_CURRENT_SITE', dotEnvReader('DOMAIN_CURRENT_SITE', 'mimotic.com'));
//define('PATH_CURRENT_SITE', dotEnvReader('PATH_CURRENT_SITE', ''));
//define('SITE_ID_CURRENT_SITE', dotEnvReader('SITE_ID_CURRENT_SITE', 0));
//define('BLOG_ID_CURRENT_SITE', dotEnvReader('BLOG_ID_CURRENT_SITE', 0));

/*
|--------------------------------------------------------------------------
| SECURITY
|--------------------------------------------------------------------------
|
|
*/
define('DISALLOW_FILE_MODS', dotEnvReader('DISALLOW_FILE_MODS', true));
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}
define('WP_AUTO_UPDATE_CORE', false);
header('X-Frame-Options: SAMEORIGIN');
header('X-Frame-Options: DENY');


/* ¡Eso es todo, deja de editar! Feliz blogging */
/**
 * WordPress absolute path to the Wordpress directory.
 */
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

/**
 * Sets up WordPress vars and included files.
 */
require_once ABSPATH . 'wp-settings.php';
