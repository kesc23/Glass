<?php

global $hooks, $styles, $actionNow, $execActions;
@$GLOBALS[ 'activePlugins' ];
global $glassDB, $glass_update;

use MartinFields\Glass\APIConsummer;
// use \Glass\GlassDB;

$glassDB      = new GlassDB;
$glass_update = new APIConsummer( 'https://api.github.com/' );

$actionNow = false;

// addHook( 'init', 'sayHello', '', 1 );
// addHook( 'init', 'sayHi', '', 1);
// addHook( 'enqueueStyles', 'enqueueStyle' );
// addHook( 'init', 'soma2', array(5, 25, 35), 0 );
addHook( 'on_start', 'verify_update' );