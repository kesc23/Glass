<?php

global $hooks;
global $styles;

addHook( 'init', 'sayHello', '', 1 );
addHook( 'init', 'sayHi', '', 1);
addHook( 'enqueueStyles', 'enqueueStyles');
addHook( 'init', 'soma2', array(5, 25, 35), 0 );