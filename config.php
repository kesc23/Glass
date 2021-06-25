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
define( 'GLASS_DIR', __DIR__ . '/' );           // Define Root Path
define( 'INCLUDES', GLASS_DIR . 'includes/');   // Define Includes Path
define( 'CLASSES', INCLUDES . 'class/' );       // Define Classes path
define( 'PLUGINS_DIR', GLASS_DIR . 'plugins/' );       // Define Classes path


/**
 * Define general constants to Glass Packages
 */
define( 'GLASS_CLASSES', true );

require_once 'CoreLoader.php';