<?php

function autoload( $classes = array())
{
    foreach( $classes as $class ):
        glassRequire( 'Class' . $class . '.php', CLASSES );
    endforeach;
    glassRequire( 'instances.php', CLASSES );
}

$classes = array(
    'Hook',
    'Styles',
    'Voyage',
    'Plugins',
    'GlassDB',
);

autoload( $classes );