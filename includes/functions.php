<?php
use MartinFields\Glass\APIConsummer;

/**
 * This Function returns preformated info of a variable, object & etc;
 * 
 * @since 0.1.0
 *
 * @param mixed $variable
 */
function prePrint_r( $variable )
{
    $variable = $variable;

    ob_start();
    ?>
    <pre class="glass_pre"><?php print_r( $variable ) ?></pre>
    <?php
    ob_end_flush();
}

/**
 * An alias for prePrint_r
 *
 * @param $variable
 * 
 * @since 0.5.0
 */
function __pre( $variable )
{
    prePrint_r( $variable );
}

function preVar_dump( $variable )
{
    $variable = $variable;

    ob_start();
        ?>
        <pre><?php var_dump($variable) ?></pre>
        <?php
    ob_end_flush();
}

function __dump( $variable )
{
    preVar_dump( $variable );
}

/**
 * This function prints all the files loaded through glass file loading functions.
 * 
 * @see Coreloader.php
 * @see glassRequire()
 * @see glassInclude()
 * 
 * @since 0.1.0
 * @since 0.6.0   changed the way it interacts with loaded file info.
 */
function file_load_log()
{
    if ( true === GLASS_DEBUG[ 'LOAD' ] ) // If Debug for files being loaded is active
    {
        global $filesLoaded;

        $dump = null;

        foreach( array_keys( $filesLoaded ) as $file ):
            $dump .= "{$file}\n";
        endforeach;

        echo "<pre>" . str_replace( '\\', '/', $dump ) . "</pre>";
    }
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

    /**
     * This function adds a plugin to the global variable $glassDB
     *
     * @param string $pluginName  required
     * @param string $author      optional
     * @param string $version     optional
     * @param string $license     optional
     * 
     * @since 0.3.0
     * @since 0.6.0  plugin are now restored in global object $glassDB
     *               instead the global variable $plugins
     */
    function addPlugin( $pluginName, $author = '', $version = '', $license = '' )
    {
        global $glassDB;

        $pluginData = array(
            'plugin name'   => $pluginName,
            'author'        => $author,
            'version'       => $version,
            'license'       => $license
        );

        $glassDB->addPlugin( new GlassPlugin( $pluginData ) );
    }

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
     * This Function once triggered, loads all plugins inside the Plugins folder.
     * Verify the existance of a valid header in the main plugin file,
     * Communicate with the database to validate, load and activate the plugin
     * 
     * @since 0.5.0
     * @since 0.6.0 now using @global $glassDB for storing the plugins.
     */
    function glassLoadPlugins()
    {
        global $glassDB;

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

        if( ! $pluginFolders ): return; endif;

        foreach( $pluginFolders as $plugin ):

            $pluginName  = basename( $plugin, '.php' );
            $thisPluginName = $pluginData[ $pluginName ]['Plugin Name']; //this plugin name in the plugin header;
            
            addPlugin( $pluginName );

            //This plugin in loop from the global Variable $glassDB
            $thisPlugin = $glassDB->getPlugin( $pluginName );
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
             * if the plugin is active on the database,
             * is now activated in @global $glassDB
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
        $content = file_get_contents( $pluginFile );

        if( ! ( 0 === strpos( $content, '<?php' ) ) ): return ''; endif;

        $block = file_get_contents( $pluginFile );

        $infoStart = strpos( $block, '/**' );
        $infoEnd   = strpos( $block, '*/' );

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

    /**
     * This function is to be used in the plugins.php page
     * (or the page you want to set up plugin config i.e. activate/deactivate ).
     * 
     * It verifies if:
     * 
     * 1 - is set the activate command, to activate a specific plugin.
     * 2 - is set the deactivate command, to deactivate a specific plugin.
     * 3 - is set the plugin delete command, to delete a specific plugin.
     * 
     * @since 0.6.0
     */
    function getPluginsState()
    {
        global $glassDB;

        if( isset( $_GET[ 'pluginActivate' ] ) ):
            $pluginToActivate = htmlspecialchars( $_GET[ 'pluginActivate' ] );
            $glassDB->activatePlugin($pluginToActivate);
        endif;
        if( isset( $_GET[ 'pluginDeactivate' ] ) ):
            $pluginToDeactivate = htmlspecialchars( $_GET[ 'pluginDeactivate' ] );
            $glassDB->deactivatePlugin($pluginToDeactivate);
        endif;
        if( isset( $_GET[ 'pluginDelete' ] ) ):
            $pluginId = htmlspecialchars( $_GET[ 'pluginDelete' ] );    
            if( $glassDB->selectThePlugin( $pluginId )['activated'] == 1 ):
                header('plugins.php');
            else:
                $glassDB->deletePlugin( $pluginId );
            endif;
        endif;
    }

}

/**
 * This plugin is usefil to gather files inside folders and subfolders.
 * 
 * it can be used for actions such as
 * deleting a folder ( which needs to be empty before deletion ).
 * 
 * @since 0.5.0
 * @since 0.6.1  optimized the way it loads the files and folders.
 * @since 0.6.2  prevented malfunction by forgetting slashes at the end of the path
 *
 * @param string $pathPrefix  the initial path for start searching. 
 * @return array              The array containing the files in folders/subfolders given
 */
function readFolders( $pathPrefix, array $ignore = [] )
{
    $ignore[ 'ignore_old' ] = $ignore;
    $pathLastCharachter = $pathPrefix[ strlen($pathPrefix) - 1 ];

    //in case the path is missing a fwr/bck slash, it will be added preventing mistakes.
    if( $pathLastCharachter != '/' && $pathLastCharachter != '\\' ):
        $pathPrefix .= '/';
    endif;

    $timeToComplete = gettimeofday()['sec'];

    $folders[$pathPrefix] = 1;
    $resultFolders[] = $pathPrefix;
    $files = array();
    $scannedFolders = array();

    foreach( $ignore[ 'ignore_old' ] as $ignored_path )
    {
        $ignore[] = $pathPrefix.$ignored_path;
    }
    unset( $ignore[ 'ignore_old' ] );

    startscan: //where we may need to iterate in case we haven't read all subfolders.
    foreach( array_keys( $folders ) as $folder ):

        $scanned = scandir( $folder );
        unset( $folders[ $folder ] );

        foreach( $scanned as $item ):

            $actualItem = $folder.$item;
    
            if( ! is_dir( $actualItem ) ):
                if( isset( $files[ $actualItem ] ) ):
                    continue;
                else:
                    $files[$actualItem] = 1;
                endif;
                continue;
            endif;

            if( $item == '.' || $item == '..' ): continue; endif;

            if( in_array( $actualItem, $ignore ) ):
                continue;
            endif;

            if( in_array( $actualItem.'/', array_keys( $folders ) ) ):
                continue;
            endif;
            $folders[ $actualItem.'/' ] = 1;
            $resultFolders[] = $actualItem.'/';
        endforeach;

        while( @$i <= count( $resultFolders ) ):
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
    

    $last = gettimeofday()['sec'];

    $timeToComplete = $last - $timeToComplete;

    $result = array(
        'time'    => $timeToComplete,
        'folders' => $resultFolders,
        'files'   => $files,
    );

    return $result;
}

/**
 * This function works similar as readFolders(), but it reads only one folder.
 * 
 * @since 0.6.2
 *
 * @param string $pathPrefix  the path to be scanned.
 * @return array
 */
function readFolder( $pathPrefix )
{
    $pathLastCharachter = $pathPrefix[ strlen($pathPrefix) - 1 ];

    //in case the path is missing a fwr/bck slash, it will be added preventing mistakes.
    if( $pathLastCharachter != '/' && $pathLastCharachter != '\\' ):
        $pathPrefix .= '/';
    endif;

    $timeToComplete = gettimeofday()['sec'];

    $folders[$pathPrefix] = 1;
    $resultFolders[] = $pathPrefix;
    $files = array();
    $scannedFolders = array();

    foreach( array_keys( $folders ) as $folder ):

        $scanned = scandir( $folder );
        unset( $folders[ $folder ] );

        foreach( $scanned as $item ):

            $actualItem = $folder.$item;
    
            if( ! is_dir( $actualItem ) ):
                if( isset( $files[ $actualItem ] ) ):
                    continue;
                else:
                    $files[$actualItem] = 1;
                endif;
                continue;
            endif;

            if( $item == '.' || $item == '..' ): continue; endif;
            if( in_array( $actualItem.'/', array_keys( $folders ) ) ):
                continue;
            endif;
            $folders[ $actualItem.'/' ] = 1;
            $resultFolders[] = $actualItem.'/';
        endforeach;

    endforeach;

    krsort( $resultFolders );

    $newFiles = array();
    
    foreach( array_keys( $files ) as $file ):
        $newFiles[] = $file;
    endforeach;

    $files = null;
    
    foreach( $newFiles as $file ):
        $files[] = $file;
    endforeach;
    

    $last = gettimeofday()['sec'];

    $timeToComplete = $last - $timeToComplete;

    $result = array(
        'time'    => $timeToComplete,
        'folders' => $resultFolders,
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
    foreach ( $folderArray as $folder ): rmdir( $folder ); endforeach;
}

/**
 * This function verifies if Glass is up to date.
 * Case positive, when required by the developer / app admin, it updates from the latest Release in Github.
 * 
 * @uses get_update()     Function to retrieve last release from Github.
 * @uses glass_update()   Function that downloads and processes the update process.
 * 
 * @since 0.7.0
 *
 * @return string
 */
function verify_update( $return = false )
{
    global $glass_update, $glass_version;

    if( ! file_exists( GLASS_DIR . 'keys/update.json' ) ):

        $the_version    = get_update( $glass_update );
        $latest_version = explode( '.', str_replace( 'v', '', $the_version->tag_name ) );

    else:
        $the_version = json_decode( file_get_contents( GLASS_DIR . 'keys/update.json' ) );

        if( $the_version->check_at <= strtotime( 'now' ) )
        {
            $the_version = get_update( $glass_update );
        }

        $latest_version = explode( '.', str_replace( 'v', '', $the_version->tag_name ) );
    endif;
    
    $this_version = explode( '.', $glass_version );

    for( $i = 0; $i < count( $this_version ); $i++ )
    {
        switch( $i )
        {
            case 0:
                $this_version  [ 'MAJOR' ] = (int) $this_version  [ $i ];
                $latest_version[ 'MAJOR' ] = (int) $latest_version[ $i ];
            break;
            case 1:
                $this_version  [ 'MINOR' ] = (int) $this_version  [ $i ];
                $latest_version[ 'MINOR' ] = (int) $latest_version[ $i ];
            break;
            case 2:
                $this_version  [ 'PATCH' ] = (int) $this_version  [ $i ];
                $latest_version[ 'PATCH' ] = (int) $latest_version[ $i ];
            break;
        }

        unset( $this_version  [ $i ] );
        unset( $latest_version[ $i ] );
    }

    $this_version  [ 'version' ] = implode( '.', $this_version );
    $latest_version[ 'version' ] = implode( '.', $latest_version );

    if( ! ( $this_version === $latest_version ) )
    {
        if( $this_version['PATCH'] < $latest_version[ 'PATCH' ] ): $msg = 'Patch'; endif;
        if( $this_version['MINOR'] < $latest_version[ 'MINOR' ] ): $msg = 'Minor'; endif;
        if( $this_version['MAJOR'] < $latest_version[ 'MAJOR' ] ): $msg = 'Major'; endif;

        $div_style = "margin: 5px auto; padding: 25px; font-family: courier, Sans Serif; text-align: center";
        printf( "<div style=\"{$div_style}\">New %s Update Available for Glass! Download now: <strong>Glass %s</strong></div>", $msg, $latest_version[ 'version' ] );

        /** 
         * To create a routine and update Glass you can use:
         * glass_update( verify_update( true ) );
         * 
         * OR
         * 
         * By default you can run it as a hook to only verify the version.
         * After, you can retrieve its data to get a path as shown above.
         */
        if( $return === true )
        {
            return $the_version->zipball_url;
        }
    }
}

/**
 * This function sends a request to the Glass Github and verifies if the actual version
 * running in the server is up to date.
 * 
 * @since 0.7.0
 *
 * @param  APIConsummer $glass_update
 * @return object
 */
function get_update( APIConsummer &$glass_update ) : object
{
    $headers = array(
        "Content-Type: application/json",
        "User-Agent: Glass",
        'Accept: application/json',
    );

    $glass_update->set_headers( $headers );
    $glass_update->set_query( 'repos/kesc23/Glass/releases' );

    $response = (object) ( $glass_update->get_request( GLASS_SSL , true , true ) )[0];

    $update = (object) array(
        'tag_name'      => $response->tag_name,
        'zipball_url'   => $response->zipball_url,
        'check_at'      => strtotime( 'now' ) + ( 3600 ),
    );

    file_put_contents( GLASS_DIR . 'keys/update.json', json_encode( $update ) );

    return $update;
}

/**
 * This function create the routine to download a .zip file and update a content inside Glass Directory.
 * 
 * @since 0.7.0
 *
 * @param  string $url    The url for the zip file to be downloaded.
 * @return void
 */
function glass_update( string $url ) : void
{
    /**
     * STEP 1:
     * 
     * This section creates the update folder case it doesn't exist.
     * Then it puts a .htacess file to protect it denying from all.
     */
    $update_folder = @opendir( GLASS_DIR .'update' );

    if( ! $update_folder )
    {
        //creates the update folder.
        mkdir( GLASS_DIR .'update', 0700, true );

        // Protects the folder
        file_put_contents( GLASS_DIR . 'update/.htaccess', 'deny from all' );
    }

    /**
     * STEP 2:
     * 
     * This step checks for the update file. If it doesn't exists, it will be downloaded
     * from github.
     * 
     * Then as it creates a main folder with (not) a good name, it is renamed to 'Glass/' and then
     * we track all files based on glass folder hierarchy.
     */
    if( ! file_exists( GLASS_DIR . 'update/glass_update.zip' ) ):

        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "User-Agent: Glass Updater"
            ),
        );

        $zip_file = file_get_contents( $url, false, stream_context_create( $opts ), 0, 999999);
        file_put_contents( GLASS_DIR . 'update/glass_update.zip', $zip_file );
    endif;

    $the_file = new \ZipArchive;
    $the_file->open( GLASS_DIR . 'update/glass_update.zip' ); 

    if( $the_file->getNameIndex( 0 ) !== 'Glass/' )
    {
        $base_folder = $the_file->getNameIndex( 0 );
        $data        = true;
        $folders     = array();
        $files       = array();

        for( $i = 0; $data !== false; $i++ )
        {
            $path = str_replace( $base_folder, '', $the_file->getNameIndex( $i ) );
            $data = $the_file->getFromIndex( $i, 999999 );

            if( @strpos( $path, '/', -1 ) ):
                $folders[] = $path;
            elseif( ! empty( $path ) ):
                $files[] = $path;
            endif;

            if( ! ( false === $data ) && ! in_array( $path, $folders ) )
            {
                $result[ str_replace( '\\', '/', GLASS_DIR . 'update/updated/glass/' ) . $path ] = $data;
            }
        }
        if( strpos( ( array_keys( $result ) )[ 0 ], '/', -1 ) ): unset( $result[ ( array_keys( $result ) )[ 0 ] ] ); endif;
    }

    $the_file->close();

    /**
     * STEP 3:
     * 
     * If there's any additional folder to be created to complete installation, it's created
     * and then the files are created or overwritten.
     */
    glass_update:

    $root_path =  GLASS_DIR;
    $updated_folder = @opendir( $root_path );

    if( $updated_folder ):

        if( ! is_dir( $root_path ) )
        {
            mkdir( $root_path, 0700, true );
        }

        foreach( $folders as $dir )
        {
            if( ! is_dir( $root_path . $dir ) )
            {
                mkdir( $root_path . $dir, 0700, true );
            }
        }

        foreach( $result as $path => $file )
        {

            if( ! file_exists( $path ) )
            {
                $created_file = fopen( $path, 'x+' );
                fclose( $created_file );
            }

            file_put_contents( $path, $file );
        }

    else:

        mkdir( $root_path, 0700, true );
        goto glass_update;

    endif;

    closedir( $updated_folder );
    closedir( $update_folder );
}

function create_jwt_file()  
{
    $jwt    = ( new Glass\GlassJWT() )::$token; // @uses GlassJWT
    $aToken = serialize( $jwt );

    file_put_contents( GLASS_DIR . 'keys/token.jwt', $aToken );

    return $jwt;
}

function create_gittoken_file( string $bearer_token, Glass\APIConsummer &$APIObject )
{
    $headers = array(
        "Content-Type: application/json",
        "User-Agent: Glass",
        'Accept: application/json',
        "Authorization: Bearer $bearer_token",
    );

    $APIObject->set_headers( $headers );
    $APIObject->set_query( 'app/installations/18639944/access_tokens' );

    $response = (object) ( $APIObject->post_request( GLASS_SSL , true , true ) );

    $gitToken[ 'token' ]      = $response->token;
    $gitToken[ 'expires_at' ] = strtotime( 'now' ) + ( 3600 );

    $gitToken = (object) $gitToken;

    file_put_contents( GLASS_DIR . 'keys/token.github', serialize( $gitToken ) );
    
    return $gitToken->token;
}

function replace_full( $variable = '' )
{
    $j = "\n<br>"; // The jumper

    $pattern = array(
        '/\<\?php/',
        '/\?\>/',
        '/\<div\>/',
        '/\<\/div\>/',
        '/\<html\>/',
        '/\<\/html\>/',
        '/\<img\>/',
        '/\<canvas\>/',
        '/\<\/canvas\>/',
        '/\<option\>/',
        '/\<span\>/',
        '/\<\/span\>/',
        '/\<style\>/',
        '/\<\/style\>/',
        '/\<script\>/',
        '/\<\/script\>/',
        '/\<pre\>/',
        '/\<\/pre\>/',
        '/ \> \n/',
    );

    $replace = array(
        "=:phptag:=",
        "=:phpendtag:=",
        "=:divtag:=",
        "=:divendtag:=",
        '=:htmltag:=',
        '=:htmlendtag:=',
        '=:imgtag:=',
        '=:canvastag:=',
        '=:canvasendtag:=',
        '=:optiontag:=',
        '=:spantag:=',
        '=:spanendtag:=',
        '=:styletag:=',
        '=:styleendtag:=',
        '=:scripttag:=',
        '=:scriptendtag:=',
        '=:pretag:=',
        '=:preendtag:=',
        '=:endtag:=',
    );

    if( is_array( $variable ) ):

        foreach( $variable as $key => $the_data )
        {
            $result[ $key ] = $j.preg_replace( $pattern, $replace, $the_data );
        }
        return $result;
    else:
        for( $i = 0; $i < count( $pattern ); $i++ )
        {
            preg_replace( $pattern[ $i ], $replace[ $i ], $variable );
        }
        return $variable;
    endif;
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