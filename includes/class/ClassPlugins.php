<?php
/**
 * @package    Glass
 * @subpackage Plugins
 * @since 0.3.0
 * @author Kesc23
 */

final class GlassPlugin
{
    private $pluginName;
    private $author;
    private $version;
    private $license;
    private $isLoaded;
    private $isActive;
    private $path;
    private $pluginFile;

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

    /**
     * This function returns the active state of the plugin
     * 
     * @since 0.5.0
     *
     * @return boolean
     */
    public function isPluginActive()
    {
        return $this->isActive;
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

    //from this point is all 0.5.0 //need documentation

    public function getPluginFile()
    {
        if( isset( $this->pluginFile ) ):
            return $this->pluginFile;
        endif;
    }

    public function setPluginPath()
    {
        $this->path = PLUGINS_DIR . "{$this->pluginName}/";
    }

    public function getPluginPath()
    {
        if( isset( $this->path ) ):
            return $this->path;
        endif;
    }
    

    public function getPluginName()
    {
        return $this->pluginName;
    }

    public function setPluginName( $pluginName )
    {
        $this->pluginName = $pluginName;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor( $author )
    {
        $this->author = $author;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion( $version )
    {
        $this->version = $version;
    }

    public function getLicense()
    {
        return $this->license;
    }

    public function setLicense( $license )
    {
        $this->license = $license;
    }

    public function updateData( array $pluginData )
    {
        $this->pluginName   = $pluginData['plugin name'];
        $this->author       = $pluginData['author'];
        $this->version      = $pluginData['version'];
        $this->license      = $pluginData['license'];
    }
}