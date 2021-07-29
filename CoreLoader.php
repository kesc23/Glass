<?php

/**
 * @global array $filesLoaded
 * 
 * Store all the file load events when the file debug is enabled
 * @since 0.6.0
 */
$filesLoaded = null;
global $filesLoaded;

/**
 * This Function loads the required files from 
 *
 * @see config.php
 * 
 * @since 0.2.0
 * @since 0.6.0  changed file load dumping/debugging.
 */
function glassInit()
{
    global $filesLoaded;

    //Always Require Core Functions to run
    glassRequire( 'functions.php', INCLUDES );

    if( true === GLASS_CLASSES ): glassRequire( 'ClassAutoLoader.php' ); endif;    

    if( isset( $filesLoaded[ CLASSES . 'ClassHook.php' ] ) ):
        addhook( 'init', 'fileLoadDebug', '', 15 );
    else:
        fileLoadDebug();
    endif;
}

/**
 * Function to require an file
 * 
 * @since 0.1.0
 * @since 0.6.0  changed file load dumping/debugging.
 *
 * @param string $fileToLoad    File to load in the program
 * @param string $directory     Path to the file. it accepts path inside constants
 */
function glassRequire( string $fileToLoad, string $directory = GLASS_DIR )
{
    global $filesLoaded;

    try{
        if ( 0 == file_exists( $directory . $fileToLoad ) ) : throw new Exception('File DOES NOT Exists');
        endif;
    } catch (Exception $e) {
        echo '<pre>Error while requiring file "'.$fileToLoad.'": ', $e->getMessage(), "</pre>";
    } finally {
        if ( 1 == file_exists( $directory . $fileToLoad ) ) :
            if( ! isset( $filesLoaded[$directory . $fileToLoad] )  ){
                require_once $directory . $fileToLoad;
                $filesLoaded[$directory . $fileToLoad] = true;
            }
        endif;
    }
}

/**
 * Function to include an file
 *
 * @since 0.1.0
 * @since 0.6.0  changed file load dumping/debugging.
 * 
 * @param string $fileToLoad    File to load in the program
 * @param string $directory     Path to the file. it accepts path inside constants
 */
function glassInclude( string $fileToLoad, string $directory = GLASS_DIR )
{
    global $filesLoaded;

    try{
        if ( 0 == file_exists( $directory . $fileToLoad ) ) : throw new Error('File DOES NOT Exists');
        endif;
    } catch (Error $e) {
        echo '<pre>Error while incluiding file "'.$fileToLoad.'": ', $e->getMessage(), "</pre>";
    } finally {
        if ( 1 == file_exists( $directory . $fileToLoad ) ) :
            if( ! isset( $filesLoaded[$directory . $fileToLoad] )  ){
                include_once $directory . $fileToLoad;
                $filesLoaded[$directory . $fileToLoad] = true;
            }    
        endif;
    }
}