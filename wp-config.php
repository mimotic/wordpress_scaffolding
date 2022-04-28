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

// lang default
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
define('AUTH_KEY',         'L5|%ZCn:rW6+mI^E%MUfP;Z1M!5bZ6OHa)u{l$ntj$l9_#5iw!,oCX?C030_8D|V');
define('SECURE_AUTH_KEY',  'eB3H)AGm@2l4j]5m??#,31XdSS3o/.[wcX,^H@Fm7,n~{td+75CU)600>lJ~x/Wx');
define('LOGGED_IN_KEY',    'N:J4=;_}0Z1US464<{C{_wG$)nph8d`XK7hx-cmE.4-+~Myn>N+E++)Ua]zWdO_e');
define('NONCE_KEY',        '1+uD|0}te`-DUK2pXi+<Z)D1MWJX^ndgJb++:E,CZZ_<3`x|AD,UL4[dIm5%0($;');
define('AUTH_SALT',        '/G[AuD`VXwJ-d*,nblh$L?dz9VY,%4hI[WK/YX3*qygJw7> 7~Jy0hz~+C_b!xib');
define('SECURE_AUTH_SALT', '@Jju;|bx|%+7D(n0TTvM=<od6Fg>fzi_P|}LeoN5J!9^w8-1|z_#{zYi(t=*jNxm');
define('LOGGED_IN_SALT',   'L^]pYKtG1H8W]GkOzrllGZqZgr%AO9{sr*;XdZEKj(q2O`m|t7ddZD`,;Uk.:qt%');
define('NONCE_SALT',       'Z7Su|e=+,S#-^9E#v6d+Zzxg%r2M?d{;I#A+E!}DhR-/qUmHGzR<!)Q@P+PshA~B');


/**
 * Prefijo de la base de datos de WordPress.
 *
 * Cambia el prefijo si deseas instalar multiples blogs en una sola base de datos.
 * Emplea solo números, letras y guión bajo.
 */
$table_prefix = dotEnvReader('TABLE_PREFIX', 'td_');

define('WP_HOME', dotEnvReader('SITE_URL', 'https://mimotic.com/'));
define('WP_SITEURL', dotEnvReader('SITE_URL', 'https://mimotic.com/'));


/**
 * Para desarrolladores: modo debug de WordPress.
 *
 * Cambia esto a true para activar la muestra de avisos durante el desarrollo.
 * Se recomienda encarecidamente a los desarrolladores de temas
 * y plugins que usen WP_DEBUG
 * en sus entornos de desarrollo.
 */

if (dotEnvReader('SITE_ENVIROMENT') === 'production') { // production server
    //ssl
    define('FORCE_SSL_ADMIN', true);
    define('FORCE_SSL_LOGIN', true);

    // debug
    define('WP_DEBUG', false);
    define('SCRIPT_DEBUG', false);
    @ini_set('display_errors', 0);
    define('WP_DEBUG_LOG', false);
    define('WP_DEBUG_DISPLAY', false);
    define('SAVEQUERIES', false);
    define( 'WP_CACHE', true );


} else if (dotEnvReader('SITE_ENVIROMENT') === 'stagin') {
    //ssl
    define('FORCE_SSL_ADMIN', true);
    define('FORCE_SSL_LOGIN', true);

    // debug
    define('WP_DEBUG', true);
    define('SCRIPT_DEBUG', true);
    define('WP_DEBUG_LOG', true);
    define('WP_DEBUG_DISPLAY', true);
    define('SAVEQUERIES', true);

} else { // local/stagin server
    // ssl
    define('FORCE_SSL_ADMIN', false);
    define('FORCE_SSL_LOGIN', false);

    // debug
    define('WP_DEBUG', true);
    define('SCRIPT_DEBUG', true);
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
// AWS CREDENTIALS
define( 'AS3CF_SETTINGS', serialize( array(
    'provider' => 'aws',
    'access-key-id' => dotEnvReader('AWS_ACCESS_KEY_ID', '*********'),
    'secret-access-key' => dotEnvReader('AWS_SECRET_ACCESS_KEY', '*********'),
) ) );

/*
|--------------------------------------------------------------------------
| PERFORMANCE
|--------------------------------------------------------------------------
|
|
*/
define('DISABLE_WP_CRON', true);
//define('EMPTY_TRASH_DAYS', 0);
//define('WP_MEMORY_LIMIT', '256M');
//define('WP_POST_REVISIONS', false);

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
