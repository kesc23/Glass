<?php

global $hooks;
global $styles;
global $actionNow;
global $execActions;
global $plugins;
$actionNow = false;

addHook( 'init', 'sayHello', '', 1 );
addHook( 'init', 'sayHi', '', 1);
//addHook( 'enqueueStyles', 'enqueueStyle' );
addHook( 'init', 'soma2', array(5, 25, 35), 0 );