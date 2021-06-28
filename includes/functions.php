<?php
/**
 * This Function returns preformated info of a variable, object & etc;
 * 
 * @since 0.1.0
 *
 * @param mixed $variable
 * @return string
 */
function prePrint_r( $variable )
{
    $variable = $variable;

    ob_start();
        ?>
        <pre><?php print_r($variable) ?></pre>
        <?php
    ob_end_flush();
}

function __pre( $variable )
{
    prePrint_r( $variable );
}

function fileLoadDebug( string $fileToLoad, string $directory = GLASS_DIR ){
    if ( true === GLASS_DEBUG[ 'LOAD' ] ): //If Debug for files being loaded is active
        echo '<pre>' . $directory . $fileToLoad . '</pre>';
    endif;
}

/**
 * Define General Functions serving Glass classes functionalities
 */

if ( true === GLASS_CLASSES )
{

    /**
     * function to Create Hook ID
     *
     * @since 0.1.0
     * 
     * @param string $tag
     * @param callable $functionToAdd
     * @return string
     */
    function createIdForHook( string $tag, callable $functionToAdd)
    {
        if ( is_string( $functionToAdd ) )
        {
            return $functionToAdd;
        }
    }

    /**
     * Function to Add Hooks 
     *
     * @since 0.1.0
     * 
     * @param string    $tag
     * @param callable  $functionToAdd
     * @param array     $acceptedArgs
     * @param integer   $priority
     */
    function addHook( string $tag, callable $functionToAdd, $acceptedArgs = array(), int $priority = 10)
    {
        global $hooks;

        if ( ! isset( $hooks[ $tag ] ) ):
            $hooks[$tag] = new Hook;
            $hooks[$tag]->addHook( $tag, $functionToAdd, $acceptedArgs, $priority);
        elseif ( isset( $hooks[ $tag ]->getCallbacks()[$priority][$functionToAdd] ) ):
            $hooks[$tag]->addHook( $tag, $functionToAdd, $acceptedArgs, $priority, 1 );
        else:
            $hooks[$tag]->addHook( $tag, $functionToAdd, $acceptedArgs, $priority);
        endif;
    }

    function doHook( string $handle )
    {
        global $hooks;
        global $execActions;

        if ( isset( $execActions[ $handle ] ) ):
            $hookError = "You cannot call <code>'{$handle}'</code>: Hook was already Called!";
            trigger_error( $hookError, E_USER_ERROR );
        else:
            $hooks[ $handle ]->callHook();
            $execActions[ $handle ] = true;
        endif;
    }

    function registerStyle( string $tag, string $pathToStyle, string $version = '')
    {
        global $styles;

        if ( ! isset( $styles[ $tag ] ) ):
            $styles[ $tag ] = new Style;
            $styles[ $tag ]->addStyle( $tag, $pathToStyle, $version);
        else:
            $styles[ $tag ]->addStyle( $tag, $pathToStyle, $version);
        endif;
        
    }

    function enqueueStyles( $handle )
    {
        global $styles;
        global $hooks;
        global $actionNow;

        try {
            if ( false === $actionNow ):
                throw new Exception('<code>'. __FUNCTION__ .'()</code> Cannot be called outside Enqueue Styles Hook');
            endif;
        } catch (Exception $error) {
            echo '<pre>Error while enqueuing styles: ', $error->getMessage(), "</pre>";
            return;
        }

        $handleArray = $styles[ $handle ];
        $styleProps = $handleArray->printStyles();

        ob_start();
            foreach ($styleProps as $style): ?>
                <link id="<?php echo $style[ 'id' ]; ?>" rel="stylesheet" href="<?php echo $style[ 'path' ] . '?' . $style[ 'version' ]; ?>">
                <?php
            endforeach;
        ob_end_flush();
    }

    function addPlugin( $pluginName, $author = '', $version = '', $license = '' )
    {
        global $plugins;

        $pluginData = array(
            'plugin name'   => $pluginName,
            'author'        => $author,
            'version'       => $version,
            'license'       => $license
        );

        $plugins[$pluginName] = new GlassPlugin( $pluginData );
    }

    /**
     * Undocumented function
     *
     * @param string $pluginFile  Plugin filename
     * @return GlassPlugin        Plugin object in the 'plugin' global variable
     *
     * function thisPlugin( $pluginFile )
     * {
     *      global $plugins;
     *      return $plugins[ $pluginFile ];
     * }
     */

    /**
     * This function returns a the plugin filename without the file extension
     *
     * @since 0.5.0
     * 
     * @param string $pluginMainFile the plugin filename
     * @return string
     */
    function pluginName( $pluginMainFile )
    {
        return basename( $pluginMainFile , '.php');
    }

    /**
     * This function returns a the plugin object
     * in the 'plugins' global variable
     * based in the plugin's filename without the file extension
     *
     * @since 0.5.0
     * 
     * @param string $filename the plugin filename
     * @return string
     * function initializePlugin( $filename )
     * {
     *      global $plugins;
     *      return $plugins[ basename( $filename , '.php' ) ];
     * }
     */
    

    /**
     * This Function once triggered, loads all plugins inside the Plugins folder.
     * Verify the existance of a valid header in the main plugin file,
     * Communicate with the database to validate, load and activate the plugin
     * 
     * @since 0.5.0
     */
    function glassLoadPlugins()
    {
        global $plugins, $glassDB;

        $glassplugins = @scandir( PLUGINS_DIR ); //scans the plugin folder

        $pluginFolders = null;

        // Define, from the folder's name, the proper plugin filename.
        if( empty( $glassplugins ) ): return; endif;
        foreach( $glassplugins as $folder ):
            switch ( $folder ) {
                case '.':
                    break;
                case '..':
                    break;
                default:
                    if( file_exists( PLUGINS_DIR . $folder . "/{$folder}.php" ) ):
                        $thePluginFile = PLUGINS_DIR . $folder . "/{$folder}.php";
                        $pluginData[ $folder ] = @getPluginInfo( $thePluginFile );
                        if( isset( $pluginData[ $folder ]['Plugin Name'] ) && ! empty( $pluginData[ $folder ]['Plugin Name'] ) ): //if the plugin has a valid header containing at least its own name
                            $pluginFolders[ $folder ] = $folder . '.php';
                        endif;
                    endif;
                break;
            }
        endforeach;

        foreach( $pluginFolders as $plugin ):

            $pluginName  = basename( $plugin, '.php' );
            $thisPluginName = $pluginData[ $pluginName ]['Plugin Name']; //this plugin name in the plugin header;
            
            addPlugin( $pluginName ); //adds this plugin in the global Variable 'Plugins'

            //This plugin in loop from the global Variable 'Plugins'
            $thisPlugin = $plugins[ $pluginName ];
            $thisPlugin->setPluginFile( $thisPlugin->getPluginName() );
            $thisPlugin->setPluginPath(); //Set the plugin main file path
            $thisPlugin->setPluginName( $thisPluginName ); //Set the plugin name based on the main plugin file header

            if( ! isset( $glassDB->selectPluginNames()[ $pluginName ] ) ):
                $glassDB->addPlugins( $pluginName );
            endif;

            //This Plugin in loop from database
            $loadedPlugins = $glassDB->selectPluginNames()[ $pluginName ];

            //Set the author name from the plugin file header
            if( isset( $pluginData[ $pluginName ]['Author'] ) ):
                $thisPlugin->setAuthor( $pluginData[ $pluginName ]['Author'] );
                $glassDB->updatePluginInfo( $loadedPlugins[ 'id' ] , 'Author', $pluginData[ $pluginName ]['Author'] );
            endif;

            //Set the license from the plugin file header
            if( isset( $pluginData[ $pluginName ]['License'] ) ):
                $thisPlugin->setLicense( $pluginData[ $pluginName ]['License'] );
                $glassDB->updatePluginInfo( $loadedPlugins[ 'id' ] , 'License', $pluginData[ $pluginName ]['License'] );
            endif;

            //Set the Plugin version from the plugin file header
            if( isset( $pluginData[ $pluginName ]['Version'] ) ):
                $thisPlugin->setVersion( $pluginData[ $pluginName ]['Version'] );
                $glassDB->updatePluginInfo( $loadedPlugins[ 'id' ] , 'Version', $pluginData[ $pluginName ]['Version'] );
            endif;

            /**
             * if the plugin is active on the db, is activated in 'plugins' global variable
             * then we load its files.
             */
            if( true == $loadedPlugins['activated'] ):
                $thisPlugin->activatePlugin();
                glassActivatePlugin( $thisPlugin );
            endif;

        endforeach;

    }

    /**
     * This function loads the required main file for the plugin
     * based on a GlassPlugin object
     *
     * @since 0.5.0
     * 
     * @param GlassPlugin $plugin
     */
    function glassActivatePlugin( GlassPlugin $plugin )
    {
        global $plugins;
        glassRequire( $plugin->getPluginFile(), $plugin->getPluginPath() );
    }
    
    /**
     * This function reads the first docblock inside a plugin file
     * the retrive from it key info about the plugin, such as:
     * Plugin Name, Author, Version and License
     * 
     * @since 0.5.0
     *
     * @param string $pluginFile  The plugin main file, to be analyzed
     * @return array              The plugin data inside the plugin header
     */
    function getPluginInfo( $pluginFile )
    {
        $block = file_get_contents( $pluginFile );

        $infoStart = strpos( $block, '/**' );
        $infoEnd = strpos( $block, '*/' );

        $pluginInfo = null;

        for( $index = 0; $index < strlen($block); $index++ )
        {
            if( $index >= $infoStart + 8 && $index < $infoEnd ):
                $pluginInfo .= $block[ $index ];
            endif;
        }

        $pluginInfo = explode( '* ', $pluginInfo );

        foreach( $pluginInfo as $descline ):
            $knewIt[] = explode( ': ', $descline );
        endforeach;
        $pluginInfo = null;
        foreach( $knewIt as $meta ):
            $pluginInfo[ trim( $meta[0] ) ] = trim( $meta[1] );
        endforeach;

        return $pluginInfo;
    }

}

/**
 * This plugin is usefil to gather files inside folders and subfolders.
 * 
 * it can be used for actions such as
 * deleting a folder ( which needs to be empty before deletion ).
 * 
 * @since 0.5.0
 *
 * @param string $pathPrefix  the initial path for start searching. 
 * @return array              The array containing the files in folders/subfolders given
 */
function readFolders( $pathPrefix )
{
    $folders[] = $pathPrefix;
    $files = array();

    startscan: //where we may need to iterate in case we haven't read all subfolders.
    foreach( $folders as $folder ):

        $scanned = scandir( $folder, 1 );

        foreach( $scanned as $item ):

            $actualItem = $folder.$item;
    
            if( ! is_dir( $actualItem ) ):
                if( ! in_array( $actualItem.'/', $files ) ):
                    $files[$actualItem] = 1;
                    continue;
                endif;
            endif;

            if( $item == '.' || $item == '..' ): continue; endif;
            if( in_array( $actualItem.'/', $folders ) ): continue; endif;
            $folders[] = $actualItem.'/';
    
        endforeach;

        while( @$i <= count( $folders ) + 1 ):
            @$i++;
            goto startscan;
        endwhile;

    endforeach;

    krsort( $folders );

    $newFiles = array();

    foreach( array_keys( $files ) as $file ):
        $newFiles[] = $file;
    endforeach;

    $files = null;

    foreach( $newFiles as $file ):
        $files[] = $file;
    endforeach;

    $result = array(
        'folders' => $folders,
        'files'   => $files,
    );

    return $result;
}

/**
 * This function deletes files in the paths parsed throught the parameter
 *
 * @since 0.5.0
 * 
 * @param array $fileArray  The paths for the files.
 */
function deleteFiles( $fileArray )
{
    foreach( $fileArray as $file ): unlink( $file ); endforeach;
}

/**
 * This function deletes folders in the paths parsed throught the parameter
 *
 * @since 0.5.0
 * 
 * @param array $folderArray  The paths for the folders.
 */
function deleteFolders( $folderArray )
{
    foreach ($folderArray as $folder ): rmdir( $folder ); endforeach;
}

function sayHello()
{
    echo 'hello';
}
function sayHi()
{
    echo 'hi';
}
function soma2( array $sum )
{
    $numbers = $sum;
    $sum = null;
    foreach( $numbers as $number )
    {
        $sum += $number;
    }
    echo $sum;
}