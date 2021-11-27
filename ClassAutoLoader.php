<?php
/**
 * This function autoloads classes inside
 *
 * @since  ?
 * @since  0.7.0 added a new autoloading method. Helping to easily integrate 3rd party libs
 * 
 * @param  array $classes
 * @return void
 */
function autoload()
{
    @ $classes = json_decode( file_get_contents( GLASS_DIR . 'autoload.json' ) );

    if( null === $classes || ! $classes ): return false; endif;

    $packages   = $classes->packages;
    $namespaces = $classes->spaces;

    $def_namespace = array();
    global $def_classlist;
    $def_classlist = array();

    foreach( $namespaces as $namespace )
    {
        $def_namespace[$namespace->name] = (object) array(
            'namespace' => $namespace->namespace,
            'path'      => $namespace->path
        );
    }

    foreach( $packages as $package )
    {
        manage_packages( $package, $def_classlist, $def_namespace );
    }

    foreach( $def_classlist as $class ):
        glassRequire( $class['file'] );
    endforeach;
    glassRequire( 'instances.php', CLASSES );
}

/**
 * This function helps to autoload the classes
 * It verifies dependencies in other classes, allowing the program to skip a loaded class.
 * @see autoload.json
 * 
 * @since 0.7.0
 *
 * @return void
 */
function manage_packages( $package, &$def_classlist, $def_namespace )
{
    if( isset( $package->requires ) && $package->requires )
    {
        if( $package->requires instanceof stdClass ):
            manage_packages( $package->requires, $def_classlist, $def_namespace );
        elseif( is_array( $package->requires ) ):
            foreach( $package->requires as $pack )
            {
                manage_packages( $pack, $def_classlist, $def_namespace );
            }
        endif;
    }
    
    if( isset( $package->name ) ):
        $to_add = array(
            'classname' => "{$def_namespace[ $package->namespace_name ]->namespace}{$package->name}",
            'file'      => "{$def_namespace[ $package->namespace_name ]->path}Class{$package->name}.php"
        );

        if( in_array( $to_add, $def_classlist ) ): return; endif;
        $def_classlist[] = $to_add;
    elseif( isset( $package->names ) ):
        foreach( $package->names as $name )
        {
            $to_add = array(
                'classname' => "{$def_namespace[ $package->namespace_name ]->namespace}{$name}",
                'file'      => "{$def_namespace[ $package->namespace_name ]->path}Class{$name}.php"
            );

            if( in_array( $to_add, $def_classlist ) ): continue; endif;
            $def_classlist[] = $to_add;
        }
    endif;
}

autoload();