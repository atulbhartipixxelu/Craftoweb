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
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u904933626_rupali' );

/** Database username */
define( 'DB_USER', 'u904933626_rupali' );

/** Database password */
define( 'DB_PASSWORD', 'S&yPXj4q' );

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
define( 'AUTH_KEY',         '^{3AKXs8e>>]iQ@L)>&;}Le7*WY6SmP7>[*S|[*Cod>h5XJePol[~4X/k~?#s=pZ' );
define( 'SECURE_AUTH_KEY',  'UIIot+/3FAo?]}7FB$R;@+c8W@2P!42.g1&u9I}8RspKgz#e..Z]m~sVT;Q]wL03' );
define( 'LOGGED_IN_KEY',    'sY>,Xh6Rn#BgE*!K@~:+!&ntc,51Ih87N/gLS)d^z/yuQ&tK|vI:a&g}M3fS;0yy' );
define( 'NONCE_KEY',        'hfK*frEL!YLr0d{PR^Qv`h(uoU~4m?:JJsZ>kd4#xCo@qT*MH`pAYZ=p7K#eA;FL' );
define( 'AUTH_SALT',        'W;3#9YoKLtFfUZ@rpj7*loOBT:|CTF73)evf2!6C#KhtF|y_.;*g,gv`fOShcobE' );
define( 'SECURE_AUTH_SALT', 'T$1:B8)9,3Xu5Y|wS`QrCYAp*^ e?fC@hHoRJk?3j.MK,mDve!:T(Q>f[Snr~!]%' );
define( 'LOGGED_IN_SALT',   'Nf+No,KY&Fuv}.Y&c,QJc~{T4G8a$NXcsG]?7@~ 9|j=s(Qrh{I268%O8E<%e8{j' );
define( 'NONCE_SALT',       'kBk][,|~0Q<2Y&Z:2abrjxs-]{mJVvrbE5!P7A:qz8(ib-lD|J@h5zLBDA6p!=FM' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
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
