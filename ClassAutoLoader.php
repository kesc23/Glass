<?php

function autoload( $class = array())
{
    $total = count($class);

    for ( $counter = 0; $counter < $total; $counter++ )
    {
        glassRequire( 'Class' . $class[$counter] . '.php', CLASSES );
    }
    require_once CLASSES . 'instances.php';
}

$classes = array(
    'Hook',
    'Styles',
    'Voyage',
);

autoload($classes);