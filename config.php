<?php

// Set to true to show loaded files/classes
define( 'GLASS_DEBUG',
    array(
        'LOAD' => true,
        'HOOK' => false,
    )
);


/**
 * Define constants for paths inside Glass
 */
define( 'GLASS_DIR', __DIR__ . '/' );             // Define Root Path
define( 'INCLUDES', GLASS_DIR . 'includes/');     // Define Includes Path
define( 'CLASSES', INCLUDES . 'class/' );         // Define Classes path
define( 'PLUGINS_DIR', GLASS_DIR . 'plugins/' );  // Define Classes path


/**
 * Define general constants to Glass Packages
 */
define( 'GLASS_CLASSES', true );


/**
 * Define the constants for the Glass Database:
 * Insert them as in your server.
 */
define( 'GLASS_HOST', '127.1.2.3:3306' );
define( 'GLASS_HOST_USER', 'root' );
define( 'GLASS_HOST_PASS', '' );
define( 'GLASS_DB', 'glass' );

require_once 'CoreLoader.php';