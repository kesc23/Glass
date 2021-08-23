<?php

// Set to true to show loaded files/classes
define( 'GLASS_DEBUG',
    array(
        'LOAD' => true,
        'HOOK' => true,
    )
);


/**
 * Define constants for paths inside Glass
 * 
 * GLASS_DIR      Define Root Path
 * INCLUDES       Define Includes Path
 * CLASSES        Define Classes path
 * PLUGINS_DIR    Define Classes path
 */
define( 'GLASS_DIR', __DIR__ . '/' ); 
define( 'INCLUDES', GLASS_DIR . 'includes/');
define( 'CLASSES', INCLUDES . 'class/' ); 
define( 'PLUGINS_DIR', GLASS_DIR . 'plugins/' );


/**
 * Define general constants to Glass Packages
 */
define( 'GLASS_CLASSES', true );


/**
 * Define the constants for the Glass Database:
 * Insert them as in your server or configure them in another file to be included before.
 * 
 * GLASS_HOST        is the mysql host with port to PDO/mysqli access. schema: server:port
 * GLASS_HOST_USER   is the mysql user admin.
 * GLASS_HOST_PASS   is the mysql user password.
 * GLASS_DB          is the mysql database that the data will be stored.
 * GLASS_SSL         For SSL Conn. Set to true to enable VERIFY_PEERS in APIConsummer cURL requests.
 */
$_constants = array(
    'GLASS_HOST'      => '127.1.2.3:3306',
    'GLASS_HOST_USER' => 'root',
    'GLASS_HOST_PASS' => '',
    'GLASS_DB'        => 'glass',
    'GLASS_SSL'       => false,
);

foreach( $_constants as $constant => $value )
{
    if( ! defined( $constant ) ): define( $constant, $value ); endif;
}

require_once 'CoreLoader.php';