<?php
/**
 * Environment File.
 * DO NOT MODIFY !!!
 * @package mimotic.io
 */
require_once __DIR__ . '/dotEnvReader.php';
/**
 * Configuración básica de WordPress.
 *
 * Este archivo contiene las siguientes configuraciones: ajustes de MySQL, prefijo de tablas,
 * claves secretas, idioma de WordPress y ABSPATH. Para obtener más información,
 * visita la página del Codex{@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} . Los ajustes de MySQL te los proporcionará tu proveedor de alojamiento web.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */
// ** Ajustes de MySQL. Solicita estos datos a tu proveedor de alojamiento web. ** //
/** El nombre de tu base de datos de WordPress */
/** The name of the database for WordPress */
define('DB_NAME', dotEnvReader('DB_NAME','mimotic') ); //
/** MySQL database username */
define('DB_USER', dotEnvReader('DB_USER','root'));
/** MySQL database password */
define('DB_PASSWORD', dotEnvReader('DB_PASSWORD','root'));
/** MySQL hostname */
define('DB_HOST', dotEnvReader('DB_HOST','www'));
/** Codificación de caracteres para la base de datos. */
define('DB_CHARSET', 'utf8');
// lang default
define ('WPLANG', 'es_ES');
/**#@+
 * Claves únicas de autentificación.
 *
 * Define cada clave secreta con una frase aleatoria distinta.
 * Puedes generarlas usando el {@link https://api.wordpress.org/secret-key/1.1/salt/ servicio de claves secretas de WordPress}
 * Puedes cambiar las claves en cualquier momento para invalidar todas las cookies existentes. Esto forzará a todos los usuarios a volver a hacer login.
 *
 * @since 2.6.0
 */
// last update 27-ABRIL-2023 by Mimotic
define('AUTH_KEY',         '4C9=9D[Iy|<RT:dD)jK4R@pO85 c*xF0o)$!SBKU^R#ri^FP9gR|bh|+hphlxU|c');
define('SECURE_AUTH_KEY',  '0>[1+v#S^.z`+sj#0Nqr~]&dPkA;tS^4t@%k0|DDw/HfIghD#{f!|2^&7>,C8Hi[');
define('LOGGED_IN_KEY',    '9IL9?U6+VGy9)XPm-J^KHNz=D!1!St2JQVnr3OQ|a{iY7g|{G1rnq!ovyA@w<h[*');
define('NONCE_KEY',        'o,2qF`$5myX8n,n{M|fzGP?Xi>I.,c@hUcU>8S.,1V_;w*Q+FE+W|Hjw.HrfjGl,');
define('AUTH_SALT',        'Bef#^iR_1T+j/0owxqYlTBZ+u-U-!zhwBuNA*yz51oMOpfYBa8_8D1Vg:y448U5B');
define('SECURE_AUTH_SALT', '*SP$7&p6Zg{,aMO1Z+*2CZ=V+1y-o(LB^Cw;5rL@kV 7Zs]u?kk]Sl(D,|@Q^A?Q');
define('LOGGED_IN_SALT',   'LL> ErI}r^wH35=5kqS^das`WCJM.>Rp5g0Mo{Y++H$vIFr#krCim0x#k[)1fG-/');
define('NONCE_SALT',       '/sR9bzs<XHImU6Sk:~rFOJl(^qZPqxFYC( wBp^ptJi?tzti$S8w<8[Hq&;(e`OT');
/**#@-*/

/**
 * Prefijo de la base de datos de WordPress.
 *
 * Cambia el prefijo si deseas instalar multiples blogs en una sola base de datos.
 * Emplea solo números, letras y guión bajo.
 */
$table_prefix = dotEnvReader('TABLE_PREFIX','wp_');
define('WP_HOME', dotEnvReader('SITE_URL','https://mimotic.com/') );
define('WP_SITEURL', dotEnvReader('SITE_URL','https://mimotic.com/') );
/**
 * Para desarrolladores: modo debug de WordPress.
 *
 * Cambia esto a true para activar la muestra de avisos durante el desarrollo.
 * Se recomienda encarecidamente a los desarrolladores de temas y plugins que usen WP_DEBUG
 * en sus entornos de desarrollo.
 */
if(dotEnvReader('SITE_ENVIROMENT') === 'production'){ // production server
    //ssl
    define('FORCE_SSL_ADMIN', true);
    define('FORCE_SSL_LOGIN', true);
    // debug
    define('WP_DEBUG', false);
    define('SCRIPT_DEBUG', false);
    @ini_set('display_errors',0);
    define( 'WP_DEBUG_LOG', __DIR__ . '/logs/wp-production-debug.log' );
    define('WP_DEBUG_LOG', false);
    define('WP_DEBUG_DISPLAY', false);
    define('SAVEQUERIES', false);
    if (!defined('WP_CACHE')) {
            }
    /* Compression */
    define( 'COMPRESS_CSS',        true );
    define( 'COMPRESS_SCRIPTS',    true );
    define( 'CONCATENATE_SCRIPTS', true );
    define( 'ENFORCE_GZIP',        true );
}else if(dotEnvReader('SITE_ENVIROMENT') === 'stagin'){
    //ssl
    define('FORCE_SSL_ADMIN', true);
    define('FORCE_SSL_LOGIN', true);
    // debug
    define('WP_DEBUG', true);
    define('SCRIPT_DEBUG', true);
    define( 'WP_DEBUG_LOG', __DIR__ . '/logs/wp-staging-debug.log' );
    define('WP_DEBUG_LOG', true);
    define('WP_DEBUG_DISPLAY', true);
    define('SAVEQUERIES', true);

}else{ // local/stagin server
    // ssl
    define('FORCE_SSL_ADMIN', false);
    define('FORCE_SSL_LOGIN', false);
    // debug
    define('WP_DEBUG', true );
    define('SCRIPT_DEBUG', true );
    define( 'WP_DEBUG_LOG', __DIR__ . '/logs/wp-local-debug.log' );
    define('WP_DEBUG_LOG', true);
    define('WP_DEBUG_DISPLAY', true);
    define('SAVEQUERIES', true);
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
//define('SENDGRID_API_KEY', dotEnvReader('SENDGRID_API_KEY','') );
//define('SENDGRID_KEY', dotEnvReader('SENDGRID_USERNAME','') );
//define('SENDGRID_USERNAME', dotEnvReader('SENDGRID_USERNAME','') );
//define('SENDGRID_PASSWORD', dotEnvReader('SENDGRID_PASSWORD',''));
//define('SENDGRID_SEND_METHOD', dotEnvReader('SENDGRID_SEND_METHOD','api'));
//
//define('SENDGRID_FROM_NAME', dotEnvReader('SENDGRID_FROM_NAME',''));
//define('SENDGRID_FROM_EMAIL', dotEnvReader('SENDGRID_FROM_EMAIL',''));
//define('SENDGRID_REPLY_TO', dotEnvReader('SENDGRID_REPLY_TO',''));
//define('SENDGRID_CATEGORIES', dotEnvReader('SENDGRID_CATEGORIES',''));

/*
|--------------------------------------------------------------------------
| AWS media deslocalizada S3
|--------------------------------------------------------------------------
|
|
*/
// AWS CREDENTIALS
//define( 'AWS_ACCESS_KEY_ID', '');
//define( 'AWS_SECRET_ACCESS_KEY', '');
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
| Iterms disable security
|--------------------------------------------------------------------------
|
|
*/
// define('ITSEC_DISABLE_TWO_FACTOR', true); // access to config
//define('ITSEC_DISABLE_MODULES', true); // remove two factor

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

define( 'WP_AUTO_UPDATE_CORE', true );
header('X-Frame-Options: SAMEORIGIN');
header('X-Frame-Options: DENY');

/**
 * Allow WordPress to detect HTTPS when used behind a reverse proxy or a load balancer
 * See https://codex.wordpress.org/Function_Reference/is_ssl#Notes
 */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

/* ¡Eso es todo, deja de editar! Feliz blogging */
/** WordPress absolute path to the Wordpress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';




