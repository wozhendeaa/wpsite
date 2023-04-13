<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'WordPress_dev' );

/** Database username */
define( 'DB_USER', 'wandr4zn_854' );

/** Database password */
define( 'DB_PASSWORD', '/0&pep8nCF7S-GS6BoGNI&/6!is22XKN' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '#KVh 9zv/S{2d<pwKTpFD=A,6)lGUi9BD.+}AyP2K|R@zccer@h^zPAjRqrLL%F@' );
define( 'SECURE_AUTH_KEY',  't6(,85X!hW@(nMkZqV&;TY2d@g~u*e+k.kH/~{r&[JiTmXmwEdbyU-rp:!%xJ|^9' );
define( 'LOGGED_IN_KEY',    'eMKQ@bK_HS!_qIVl:I1Z `5BY/1 PTY -Lk9WVydanDz?PM3H*,SHb|u@;z})XI_' );
define( 'NONCE_KEY',        'Q-(z# $ax5mXv6llvCSW.ULFJ{bsPLb<8Vj6/zrQE4|J~[;2r6 w|l$d~:;sX~F,' );
define( 'AUTH_SALT',        'Q>A5 |s0lDa.qzgcQ&27 aU*59>S6-QJIzmN~n09Cg26olO%}02ux_$Dhe`zoX[l' );
define( 'SECURE_AUTH_SALT', 'qf+QU>xov}^)VlJBDrk<x-t-O/TkE^@)vjq}`y95QJ^;Y~Z:lM6uw{9KbJQds8!g' );
define( 'LOGGED_IN_SALT',   '!r()TjMfwy{:Vh~a|^e)o@D~;-Nh1 UO#8 (/&~k6TcKH_Mqi LX7UvjhX_CMoq]' );
define( 'NONCE_SALT',       '%eY@>od$!6eYprY71;t+9H,]q)UY^IBA.C/g>;iOl8K(2bQ[@bLOEHts L#lc#61' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */

define('WP_HOME', 'http://localhost/wordpress');
define('WP_SITEURL', 'http://localhost/wordpress');

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
