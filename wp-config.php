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
define('DB_NAME', 'galina');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'Aa123456');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Obz),2_al1K5o9vw4way8$s*/Sf2R0SL~=3V&v$^1QQsZMJD0J`%HL(_ C(p&|*)');
define('SECURE_AUTH_KEY',  'deVeG(dpTdMz,_O|Oxupv!+u]8cus+_{$-t;1?pA?K1a|r>)ffem3t2^QzV_/[AN');
define('LOGGED_IN_KEY',    'z^u3Vv#/X;RLg Cyc{`aC qliuXK+$K@]z0W9>#tyW[|J-sh18n 2Q)f`=,:P{JA');
define('NONCE_KEY',        '!_$LaL^3,2T}-H@cD>p}MX[xTwK/l2RbTQz:*Z~Wzq[!?T3tV_[ky0gpMn.O{z)$');
define('AUTH_SALT',        '*0oRp{7X&:F^iWx4Fjpty7vK-g}p-1XTUh8]]XdE^+@GC[Umd.v*)v_;%oMD?y9z');
define('SECURE_AUTH_SALT', 'Khsjmp*Cc%XZnMvK67LpQx6xq=.m!=HBht2kuR2GpK(C<$KwA`4ALbVjGY&;.EL<');
define('LOGGED_IN_SALT',   '6pt%q;`;8E_O8fa42+Fwjjs13VR<vn0DMAauNk.1(8VCVV3l G^2eZUV6FoG$DYh');
define('NONCE_SALT',       '6-%g(xWMf=d8D+u:$c:qWhrx2ws.FcaNLm$qed:q-mI?5-&.G:+cGzU]1]Hqh_g2');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
