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

    function thisPlugin( $pluginFile )
    {
        global $plugins;
        return $plugins[ $pluginFile ];
    }

    function pluginName( $pluginMainFile )
    {
        return basename( $pluginMainFile , '.php');
    }

    function glassLoadPlugins()
    {
        $glassplugins = scandir( PLUGINS_DIR );

        $pluginFolders = null;

        foreach( $glassplugins as $folder ):
            switch ( $folder ) {
                case '.':
                    break;
                case '..':
                    break;
                default:
                    $pluginFolders[ $folder ] = $folder . '.php';
                break;
            }
        endforeach;

        foreach ($pluginFolders as $folder => $plugin):
            glassRequire( $plugin, PLUGINS_DIR . $folder . '/' );
        endforeach;
    }
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