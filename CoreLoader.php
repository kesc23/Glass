<?php

/**
 * This Function loads the required files from 
 *
 * @see config.php
 * 
 * @since 0.2.0
 */
function glassInit()
{
    //Always Require Core Functions to run
    glassRequire( 'functions.php', INCLUDES );

    if ( true === GLASS_CLASSES )
    {
        glassRequire( 'ClassAutoLoader.php' );
    }
}

/**
 * Function to require an file
 *
 * @param string $fileToLoad    File to load in the program
 * @param string $directory     Path to the file. it accepts path inside constants
 */
function glassRequire( string $fileToLoad, string $directory = GLASS_DIR )
{
    try{
        if ( 0 == file_exists( $directory . $fileToLoad ) ) : throw new Exception('File DOES NOT Exists');
        endif;
    } catch (Exception $e) {
        echo '<pre>Error while requiring file "'.$fileToLoad.'": ', $e->getMessage(), "</pre>";
    } finally {
        if ( 1 == file_exists( $directory . $fileToLoad ) ) :
            require_once $directory . $fileToLoad;
            fileLoadDebug( $fileToLoad, $directory );
        endif;
    }
}

/**
 * Function to include an file
 *
 * @param string $fileToLoad    File to load in the program
 * @param string $directory     Path to the file. it accepts path inside constants
 */
function glassInclude( string $fileToLoad, string $directory = GLASS_DIR )
{
    try{
        if ( 0 == file_exists( $directory . $fileToLoad ) ) : throw new Error('File DOES NOT Exists');
        endif;
    } catch (Error $e) {
        echo '<pre>Error while incluiding file "'.$fileToLoad.'": ', $e->getMessage(), "</pre>";
    } finally {
        if ( 1 == file_exists( $directory . $fileToLoad ) ) :
            include_once $directory . $fileToLoad;
            fileLoadDebug( $fileToLoad, $directory );
        endif;
    }
}