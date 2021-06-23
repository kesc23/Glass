<?php

require 'config.php' ;
glassInit();

global $hooks;

global $styles;

registerStyle( 'normalStyle', __DIR__ . '/class/style.css' );
registerStyle( 'normalStyle', __DIR__ . '/class/style.css', '1.0' );

enqueueStyles( 'normalStyle' );

$hooks['init']->callHook();

prePrint_r($hooks);