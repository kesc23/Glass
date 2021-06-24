<?php

function autoload( $class = array())
{
    $total = count($class);

    for ( $counter = 0; $counter < $total; $counter++ )
    {
        glassRequire( 'Class' . $class[$counter] . '.php', CLASSES );
    }
    glassRequire( 'instances.php', CLASSES );
}

$classes = array(
    'Hook',
    'Styles',
    'Voyage',
);

autoload( $classes );