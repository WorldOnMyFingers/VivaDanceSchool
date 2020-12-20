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
define('DB_NAME', "vivanew");


/** MySQL database username */
define('DB_USER', "root");


/** MySQL database password */
define('DB_PASSWORD', "root");


/** MySQL hostname */
define('DB_HOST', "localhost");


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
define('AUTH_KEY',         'lK^sClq-|DegM<1&d)(+o?apm8)$0YkCtjywMNEp^>8[Q086<3LU(~-a0aS=%`Da');

define('SECURE_AUTH_KEY',  '!XnCm7GyvU^iC(,|qeQtgEDmUegz(Yc~$=-~,htoPxP1J;Z|p|!f[T$(FeQ1<+[.');

define('LOGGED_IN_KEY',    'gKPSv3gM.%~c#lqAxfRpTuGzMjSD8OI<Spm9`P$SpI^Cy&N1IG[Z-]//}T&S-T-9');

define('NONCE_KEY',        'B=-k0=dMSWBN+|,g@enUT!sfgs451<(1)_4RD/Fu@AO1qBwpE4EJBx}ead78iRUG');

define('AUTH_SALT',        ']4&w]n%n+OT1UdD|4;-X.4H[<PE=,|H|HJ>|/PREif!`clUb1Fshl5Z acU+D~@%');

define('SECURE_AUTH_SALT', '[LTTiM~TT~plD|fhN7zKTj]$.%]6f3dh+:aV$Yx;-qvo/O=`K7)k8mGU/~Z.>S/,');

define('LOGGED_IN_SALT',   'QM?bGVJ;BW`s+Mj1t<-Y%Y4Qg<4pXT|0+0+5O,A+@tTd+4n0ehD3o9PLSVGx|:Dx');

define('NONCE_SALT',       'cu*kWMCr-9CrZUC5mNL$x_}Z<zSrCBpqb+z`JKhw357EP@z)|{A{v&6#fCTQN]r|');


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'viva19wp_';


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
