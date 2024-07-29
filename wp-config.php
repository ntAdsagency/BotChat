<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
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
define( 'DB_NAME', 'eshop' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'vVJp;xYrjR2Zq^Ts^?wH TS*7$+][t9l8u3vmMR+Z!bUo^?^d?U:H~}{:&LHz{d2' );
define( 'SECURE_AUTH_KEY',  '!Si*-R,bA?x%VJT*)W3a.#e/#XBs]jLYNhFA!P!y0q#s]#V:n,[{h%/$kC.DKVUQ' );
define( 'LOGGED_IN_KEY',    'kXI~ti)~Q_%`Rh6$% +2E&n_y.>|$!}| ~XJKaDl)vS~-xK|@E9L_$c@*W(Hr2YS' );
define( 'NONCE_KEY',        '|pvi{nR?^;ob(*zYb?VidffiEtTr7[jlsM^YZec+q|q@im~o{yi}[8oZ@fN!vr>,' );
define( 'AUTH_SALT',        'F=9*J).MV.+;EHIh>u]*RNK.]]7P8uu:a84P.s#T<^kEe3pyU~`UQA01WUTR-Boo' );
define( 'SECURE_AUTH_SALT', 'c;jS8M6u]OWnD/N+}%/)xzek!KikVC9<(p(Gp,S76vY8[(Ysu_+(:6uhwgNmUxb2' );
define( 'LOGGED_IN_SALT',   'wq{&!L2,t)CyVEZ2eccI2%M$:C#FO#7QS/|Q%!j;g,S5F4x90:R5GGERt:qC]aHp' );
define( 'NONCE_SALT',       'mm6m4D%t*owe-R*`E%|,Lnj$LiJrk)!H|o@H}Y8rx*&|+.va;P9p&X|Wuk{PA99q' );

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



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
