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

    ob_start()
    ?>
    <pre><?php print_r($variable) ?></pre>
    <?php
    ob_end_flush();
}

function fileLoadDebug( string $fileToLoad, string $directory = GLASS_DIR ){
    if ( true === DEBUGLOAD ):
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
     * @param string $tag
     * @param callable $functionToAdd
     * @param array $acceptedArgs
     * @param integer $priority
     */
    function addHook( string $tag, callable $functionToAdd, $acceptedArgs = array(), int $priority = 10)
    {
        global $hooks;

        if ( ! isset( $hooks[ $tag ] ) ):
            $hooks[$tag] = new Hook;
            $hooks[$tag]->addHook( $tag, $functionToAdd, $acceptedArgs, $priority);
        else:
            $hooks[$tag]->addHook( $tag, $functionToAdd, $acceptedArgs, $priority);
        endif;

    }
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

function enqueueStyles( string $handle )
{
    global $styles;

    $styleProps = $styles[$handle]->printStyles();

    ob_start();
    foreach ($styleProps as $style): ?>
    <link id="<?php echo $style['id']; ?>" rel="stylesheet" href="<?php echo $style['path'] . '?' . $style['version']; ?>">
    <?php
    endforeach;
    ob_end_flush();

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