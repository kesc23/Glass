<?php

global $hooks;
global $styles;
global $actionNow;
global $execActions;
global $plugins;
@$GLOBALS[ 'activePlugins' ];
global $glassDB;
$glassDB = new GlassDB;

//__pre( $GLOBALS['activePlugins'] );

$actionNow = false;

addHook( 'init', 'sayHello', '', 1 );
addHook( 'init', 'sayHi', '', 1);
//addHook( 'enqueueStyles', 'enqueueStyle' );
addHook( 'init', 'soma2', array(5, 25, 35), 0 );