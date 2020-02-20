<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'itrems_db' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         ':(J-_nf>9.+tH7k[T-v0l!MUz5l@^l46F3xQU_/,Q p&Ak{;y,?[*WFw>5ufan=4' );
define( 'SECURE_AUTH_KEY',  'V/1oWE-*{5](*^r)d=QHVP&4A#IU*v@E]3Vy;^<xl,3Iy2ujEjDf=X[,9c@] HGb' );
define( 'LOGGED_IN_KEY',    'r(KXCb2taF{j|yrR&+H; n?IV1SKZ4oR(qP%vA==cY.Is!k9rX@)rMrGy<}HO>E5' );
define( 'NONCE_KEY',        '{0=D+2tgrHuN||KP5BXyzOCkq]*ZL{?pApEMRJf;uF|f+wOHe|Z5ArGn}z+[g!xh' );
define( 'AUTH_SALT',        'T]mj F#e)A%hrZN3F0@]9d=[#N,+kO*[-gEsGLQd &hu[FEJ%@=>jW5.F~7{%K66' );
define( 'SECURE_AUTH_SALT', '4sotF]Cor<J7Tqf![NREWk#-:W.TP[n!_X6{ x<3xed<pOM!XSdhBZ6I!cd<$QaT' );
define( 'LOGGED_IN_SALT',   'TC41xXV/sN Br>ve~Wq8EU&9b8 ,#J@6:-Ygkb)CXuJ6@e}7Mo!<zCIy@oQ8qV[X' );
define( 'NONCE_SALT',       'nJ_F!wE8@M=WhT?E9Z0J eF(!^-r.7j@Zb[Z<1L|W>(mm*+f8_F4XVXp&BZ_+E>;' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
