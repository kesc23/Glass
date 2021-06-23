<?php

// Set to true to show loaded files/classes
define( 'DEBUGLOAD', false );


/**
 * Define constants for paths inside Glass
 */

// Define Root Path
define( 'GLASS_DIR', __DIR__ . '/' );
// Define Includes Path
define( 'INCLUDES', GLASS_DIR . 'includes/');
// Define Classes path
define( 'CLASSES', INCLUDES . 'class/' );
/**
 * Define general constants to 
 */

define( 'GLASS_CLASSES', true );

require_once 'CoreLoader.php';