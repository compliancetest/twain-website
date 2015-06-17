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
define('DB_NAME', 'compliancetestfront');

/** MySQL database username */
define('DB_USER', 'dbadmin');

/** MySQL database password */
define('DB_PASSWORD', 'W26pgueXAbMv7PdoJlTz');

/** MySQL hostname */
define('DB_HOST', 'compliancetest.cvno0ugmoa4w.ap-southeast-2.rds.amazonaws.com');

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
define('AUTH_KEY',         'L+:Ua.<+8.m`D=P7g>GXi*9Gk*c?qJxL&~xd?kR3R2/S6-Z[m!ar`kxJ_cX @A1T');
define('SECURE_AUTH_KEY',  'Pe+<Qg_]t.wMgLSGop(Jxi?C)QS:4]>|Os44u^&JR67aZS$7)Dz95hg}w9`yifRR');
define('LOGGED_IN_KEY',    '|;9>w<H}n=~Y$]-6?XC&296Qm59+OMnbQ*{PkXHDa2->Q&Wj`ssCUMm0ug^`|w:;');
define('NONCE_KEY',        '-t3P# OV6c7M[-_D4^qz`lRsXT0`emX[e1B2o&R{+qe=l6jZd*J|$)DBUXg##/B;');
define('AUTH_SALT',        'vE1E=KuWje{+x|qv{^B-Q7R&FcN=zVb8wnh9]qE&vpU<f%%g|~>L5ZtILfsti{nC');
define('SECURE_AUTH_SALT', 'e)^{20%Bs)j6%^MAFa;|p@CGs|(qlAA}M-<r %fn0}pOL]CydsJs3/!GB0E7EC*J');
define('LOGGED_IN_SALT',   ';K4}i.hWhCt@y8xK]H?BZuT4H.naT<KI+U4-c~$<bjp!ZDa6PzMkcV%=^d9*?W!h');
define('NONCE_SALT',       '{*tj$~TQ=J8+FUy2-~V!z{My|+,e/`l _96sg`xE*qf#m~-Pqh,N#9c(JC||JSq:');

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

