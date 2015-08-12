<?php
/* Define memory limit */
define('WP_MEMORY_LIMIT', '128M');
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'Helloperth');

/** MySQL database username */
define('DB_USER', 'Helloperth');

/** MySQL database password */
define('DB_PASSWORD', 'Hel#$$%');

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
define('AUTH_KEY',         '%mNvznPl_|DWVKV~)3]|.g`9{C!|InY^l?&]W,?5.2xoE(e-GL*?Y:#MN}I pJcz');
define('SECURE_AUTH_KEY',  'w%;lhn#z?:-?AOn|k}A9;oI|#mO,lJhFZ@e1Y`B#t}ANt p)63no5;b#2GEhoEh:');
define('LOGGED_IN_KEY',    ']JUQBhJr!?a6|(!*WKJ5JKYJS,PsE|;uiLgzf#>{e2T8&k+ UP-{+?w-@85<U/-Y');
define('NONCE_KEY',        'f,``#>5>KqSADxQ?h{ 0h]G[JP6N]|Z?6?)$*XbU[hkl020~x|cygF`c=vyZJX{t');
define('AUTH_SALT',        'c|BD#8jS~[e]bJ91J1J-@:`H]`V.{w|W>fA>P v,(V,e1@*j7m?s1L@}?(|V|4|@');
define('SECURE_AUTH_SALT', 'i)-nTDB>1`l2kai0B/IX+/+;4=j;fvJyiaR?-yO~?kb/]jyj?%|W~k5nzMhv&#MQ');
define('LOGGED_IN_SALT',   'mKQ9M|p[F@XN0AEW04VTYLklQ[y?urMM;Sp-h+w3asEh5G:|Dj(/fyzb3mEz1d5%');
define('NONCE_SALT',       '-n,L+8EXJt%d|wR--^*D=wjy|MN?R4NMX!J]u;WB~AZ?1*vlR1V:]2j+m?+l6>ml');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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

/* define page templates */
define('HOME_PAGE', 5);
define('LOGIN_PAGE', 74);
define('REGISTRATION_PAGE', 76);
define('FORGOT_PASSWORD_PAGE', 78);
define('RESET_PASSWORD_PAGE', 80);
define('PROFILE_PAGE', 82);
define('ADVERTISE_WITH_US_PAGE', 106);
define('MAKE_PAYMENT_PAGE', 108);
define('ADD_DIRECTORY_PAGE', 132);
define('EDIT_DIRECTORY_PAGE', 193);
define('ADVANCED_SEARCH_PAGE', 170);
define('NEWS_N_EVENTS_PAGE', 181);
define('CHECKOUT_PAGE', 195);
define('CONFIRM_SUBSCRIPTION_PAGE', 197);
define('SUBSCRIPTION_COMPLETE_PAGE', 211); // 260
define('NEWS_PAGE', 260);
define('GUIDE_PICKUP_LOCATIONS_PAGE', 373);
define('MAPS_PAGE', 282);
define('USER_GUIDE', 865);

/* HOME PAGE */

define('EXCLUSIVE_DISCOUNT_PAGE', 272);
define('EVENTS_IN_PERTH_PAGE', 276);
define('TOURIST_INFO_PAGE', 278);
define('GENERAL_INFO', 284);
define('PERTH_TRANSIT_MAPS', 367);
define('TRANSPORT_PAGE', 399);

/* Time Format */

define('DATE_DISPLAY_FORMAT', 'jS M, Y');
define('DATE_DATABASE_FORMAT', 'Y-m-d');
define('DATETIME_DISPLAY_FORMAT', 'jS M, Y h:i a');
define('DATETIME_DATABASE_FORMAT', 'Y-m-d H:i:s');

/* wp stop autometic update */
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'WP_AUTO_UPDATE_CORE', false );

/* wp disabled trash */
//define('EMPTY_TRASH_DAYS', 0);

/* Bypass ftp for auto update */
define('FS_METHOD','direct');

if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
