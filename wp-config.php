<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
//define('DB_NAME', 'negosolu_compliance');
define('DB_NAME', 'testcompliance');

/** MySQL database username */
//define('DB_USER', 'negosolu_comp');
define('DB_USER', 'testcompliance');

/** MySQL database password */
//define('DB_PASSWORD', 'o!E6ds@w+.?;');
define('DB_PASSWORD', 'cb8682e0ff4721');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'Jz@iT(D5m-aqa2gCp]8S|B??lbBaj?6df4+o:)z*z:fGY-XZg}!b+E+3oH]koYS)');
define('SECURE_AUTH_KEY',  '|t0E{;HO{n(y72~7|8fz~zgoaHUEu7t]FX{<?t[qnmSYhY(@Wy?~$Zw]v66KndW|');
define('LOGGED_IN_KEY',    'k|. _qTqzQcz7RCP5zC|V||E+0`[3o+BR)Ht6Z^uW>k$k`F(c:M&]]KUh/+do(9U');
define('NONCE_KEY',        'o}yLp:K~iaq3JaI-/R0Eef.0mB@|j+`7D!.Ag1KrhfSxkN]:<}wdS.5W~^9pp;Bj');
define('AUTH_SALT',        'kR&@>vN$IjE<]:YU_X+u]!SIeFhkYBF%??+jGH`dEvly6?n=/my~~?/XEv`jE}Y&');
define('SECURE_AUTH_SALT', 'E@1O&,E5dbAkQ *XP-QY*:bm*At[v#vW_+||l{2rTD/!7)7EYL_Dr3>q`m7n]fI,');
define('LOGGED_IN_SALT',   '}Ux1O!,MdeLrk-s$CbR*+.r+b .KVwXStjfX2)BR>/ TD2SwF*jh<Mwr;^)4l^F-');
define('NONCE_SALT',       '}zQN+oZRov;mZBc+y1^e40t<R|.~-40Ra9(:.7UUQ}l6.$$5>,Kk-P7b98eTZ=Dj');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */

define('WP_DEBUG', true);


define( 'BP_DOCS_SLUG', 'wiki' );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
