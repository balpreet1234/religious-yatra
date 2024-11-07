<?php
define( 'WP_CACHE', true );
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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u997496973_HIs2v' );

/** Database username */
define( 'DB_USER', 'u997496973_iE6rW' );

/** Database password */
define( 'DB_PASSWORD', 'GjOQeHVJev' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'HRTTZAm93IV/w9FQGn^wh>FQzJ7C{bcE?hh3.{?160Y}]b>HSb;ey6|cI^oP:3R+' );
define( 'SECURE_AUTH_KEY',   'vpiytdH2m|]puPVY|&1>wLY?<S6^5:uq ^#;L*5LLziL|`>Xd>2w+h[ImyCxe:a*' );
define( 'LOGGED_IN_KEY',     'J4ww5!%1q?HVP<H]#Avn#:#tpf/2%LSj|Kh`*pu7I2L8-jDnQKU7?3lQU([7ID6-' );
define( 'NONCE_KEY',         '%|s5L=RcoUll<u`ck#zjqOYm@^+P5`{/ A@C)}6YPHM s[Z:PD8Itq+r0o/BoI#-' );
define( 'AUTH_SALT',         '-6fG=x9&t%w(Fs3cl1D/g^j-Zs  f9ww(&yd`uw8kV>GaA37uUGK]du&7GK3-=Ki' );
define( 'SECURE_AUTH_SALT',  '-{,j}x}Q :.bWJphXrjbJ?IMCmlk1o]Nr0dkAJ;Posf0LP/T?Ar#_0:~0LpP`_[{' );
define( 'LOGGED_IN_SALT',    'D*cdkI7StTlwCPK1(w8?C  9g07Icwxk# lT(#Ues2h?YK7B=s(d{F?HQL$7?s|u' );
define( 'NONCE_SALT',        '!uG8GRk-e5Tl,%E%sNyp;4;,k$UD(W*33^Gsi4&hq!!|8k Loe8!T?-&V#i)F%eY' );
define( 'WP_CACHE_KEY_SALT', '#{U)jxZhT3aoZ3MTcxXj)&!C/p(ihmnIkH6bl~s})hm?E]`lt3p45@c`~ngG-j:.' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'FS_METHOD', 'direct' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
