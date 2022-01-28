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
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
define("WP_CACHE", true);
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'variphy' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
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
define( 'AUTH_KEY',         ':U^spKhNG!;/s-=xgIq=L0~pOQMTLgA}F%Ez2OBh7qX;W![XWi8xXF2J3)W9yOFz' );
define( 'SECURE_AUTH_KEY',  'f3!dLL7wl1@mQLQ03`-Z/]`-j!fevj/A:3`}O1PH6esqqLc9/oFq7)yys=:g1;SR' );
define( 'LOGGED_IN_KEY',    '8 [q@#o<ApP>A6GVW.qP`jJg/{;)vJJOwC;g5}D_&=)+S>3bg]?xhn`;jIlXVyc1' );
define( 'NONCE_KEY',        'WMc=>L@&GDwzQI$4BbO3tN:n*ai(H+^trlR&5UHH}-C~@ak.oFn}$F`>%VQU*6?+' );
define( 'AUTH_SALT',        '4]>MPE2Viu|N<>@WB:laxI1{y~gU+#6}DaB:UP)2|PK,IVq>~>dl@-:A{l5c,k1p' );
define( 'SECURE_AUTH_SALT', 'E8tFGL3*mBav}V4:[&]>lkBbK6#W|7=q<i*6@&(}C8lUq-w.rq]gT$:KV53&t%)j' );
define( 'LOGGED_IN_SALT',   'aM^!S0@:Oum@[(hUyd_kU9uhcOM-Fjkpa}]HQ1cEg-i9zvU?7U:JZMKgcTT$!Q[2' );
define( 'NONCE_SALT',       'D7@ekaEDgr4M}T&f*t+/tsKCI0gVZ4mY7_pOUouoH)cR|CN!Cv~L$2F~e.Hq5(c]' );

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';


define( 'SUBDOMAIN_INSTALL', false );