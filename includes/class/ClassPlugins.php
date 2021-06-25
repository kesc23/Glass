<?php
/**
 * @package Glass
 * @subpackage Plugins
 * @since 0.3.0
 * @author Kesc23
 */

final class GlassPlugin
{
    private string  $pluginName;
    private mixed   $author;
    private mixed   $version;
    private mixed   $license;
    private bool    $isLoaded;
    private bool    $isActive;
    public  string  $path;
    private string  $pluginFile;

    public function __construct( array $pluginData )
    {
        $this->pluginName   = $pluginData['plugin name'];
        $this->author       = $pluginData['author'];
        $this->version      = $pluginData['version'];
        $this->license      = $pluginData['license'];

        $this->isLoaded     = true;
    }

    public function activatePlugin()
    {
        $this->isActive = true;
    }

    public function deactivatePlugin()
    {
        $this->isActive = false;
    }

    public function setPluginFile( $filename )
    {
        global $plugins;

        if( isset( $this->pluginFile ) ) :
            trigger_error( __METHOD__ . ' cannot redefine plugin file', E_USER_ERROR );
        else:
            $this->pluginFile = $filename . '.php';
        endif;
    }
}