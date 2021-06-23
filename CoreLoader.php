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

function glassRequire( string $fileToLoad, string $directory = GLASS_DIR )
{
    try{

        if ( 0 == file_exists( $directory . $fileToLoad ) )
        {
            throw new TypeError('Arquivo Inexistente');
        }

    } catch (Exception $e) {
        echo 'Erro Ao Carregar Arquivo: ', $e->getMessage(), "\n";
    } finally {

        require_once $directory . $fileToLoad;
        
        fileLoadDebug( $fileToLoad, $directory );
    }
}

function glassIncludes( string $fileToLoad, string $directory = GLASS_DIR )
{
    try{

        if ( 0 == file_exists( $directory . $fileToLoad ) )
        {
            throw new TypeError('Arquivo Inexistente');
        }

    } catch (Exception $e) {
        echo 'Erro Ao Carregar Arquivo: ', $e->getMessage(), "\n";
    } finally {

        include_once $directory . $fileToLoad;
        
        fileLoadDebug( $fileToLoad, $directory );
    }
}