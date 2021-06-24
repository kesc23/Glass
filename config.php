<?php

// Set to true to show loaded files/classes
define( 'GLASS_DEBUG',
    array(
        'LOAD' => false,
        'HOOK' => true,
    )
);


/**
 * Define constants for paths inside Glass
 */
define( 'GLASS_DIR', __DIR__ . '/' );           // Define Root Path
define( 'INCLUDES', GLASS_DIR . 'includes/');   // Define Includes Path
define( 'CLASSES', INCLUDES . 'class/' );       // Define Classes path


/**
 * Define general constants to Glass Packages
 */
define( 'GLASS_CLASSES', true );

require_once 'CoreLoader.php';