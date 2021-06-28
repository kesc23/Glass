<?php
/**
 * The class to acess the database.
 * 
 * @package    Glass
 * @subpackage GlassDB
 * 
 * @since 0.5.0
 * @author Kesc23
 */
final class GlassDB
{
    private $db_host;      //The host
    private $db_user;      //the host user
    private $db_password;  //the host user passoword
    private $db_name;      //Database name;
    private $connection;   //the connection data for communicating with the SQL server
    
    //our tables
    private $plugins; //used to verify the plugins inside the program;

    /**
     * This function sets the MySql server host name
     * based in a constant defined in config.php
     * 
     * @see config.php
     *
     * @since 0.5.0
     */
    private function setHost()
    {
        $this->db_host = GLASS_HOST;
    }

    /**
     * This function sets the MySQL server user based in a constant defined in config.php
     * 
     * @see config.php
     *
     * @since 0.5.0
     */
    private function setUser()
    {
        $this->db_user = GLASS_HOST_USER;
    }

    /**
     * This function sets the MySQL server password
     * based in a constant defined in config.php
     * 
     * @see config.php
     *
     * @since 0.5.0
     */
    private function setPassword()
    {
        $this->db_password = GLASS_HOST_PASS;
    }

    /**
     * This function sets the Database name based in a constant defined in config.php
     * 
     * @see config.php
     *
     * @since 0.5.0
     */
    private function setDBName()
    {
        $this->db_name = GLASS_DB;
    }

    public function __construct()
    {
        $this->setHost();
        $this->setUser();
        $this->setPassword();
        $this->setDBName();

        // Creating the connection with the database
        $connection = $this->getConnection();

        // Creating the plugins database
        if( $this->selectPlugins() === null ):
            $this->createTable( 'plugins' , array(
                'id'        => 'INT(3) PRIMARY KEY AUTO_INCREMENT,',
                'name'      => 'VARCHAR(80),',
                'author'    => 'VARCHAR(30),',
                'license'   => 'VARCHAR(15),',
                'version'   => 'VARCHAR(13),',
                'activated' => 'TINYINT(1)'
            ) );
        endif;
    }

    /**
     * This function return an array with all the plugins tagged by plugin id.
     *
     * @since 0.5.0
     * 
     * @subpackage   GlassDB
     * @subpackage   Plugins
     * @return array
     */
    public function selectPlugins()
    {
        $stmt = $this->getConnection()->prepare('SELECT * FROM plugins');
        $stmt->execute();
        $allPlugins = $stmt->fetchAll( PDO::FETCH_ASSOC );

        $pluginsResult = null;
        foreach( $allPlugins as $plugin ):
            $pluginsResult[ $plugin['id'] ][ $plugin['name'] ] = $plugin;
        endforeach;
        return $pluginsResult;
    }

    /**
     * This function return an array with all the plugins tagged by plugin name.
     *
     * @since 0.5.0
     * 
     * @subpackage   GlassDB
     * @subpackage   Plugins
     * @return array
     */
    public function selectPluginNames()
    {
        $stmt = $this->getConnection()->prepare('SELECT * FROM plugins');
        $stmt->execute();
        $allPlugins = $stmt->fetchAll( PDO::FETCH_ASSOC );

        $pluginsResult = null;
        foreach( $allPlugins as $plugin ):
            $pluginsResult[ $plugin['name'] ] = $plugin;
        endforeach;
        return $pluginsResult;
    }

    /**
     * This function select a specific plugin from id.
     *
     * @since 0.5.0
     * 
     * @param integer $pluginId
     * @subpackage    GlassDB
     * @subpackage    Plugins
     * @return array  The array containing the data of the plugin.
     */
    public function selectThePlugin( $pluginId )
    {
        $sql  = "SELECT * FROM `plugins` WHERE `id` = '{$pluginId}'";
        $stmt = $this->getConnection()->prepare( $sql );
        $stmt->execute();

        $thePlugin = $stmt->fetchAll( PDO::FETCH_ASSOC );
        
        $returnPlugin = null;

        foreach( $thePlugin as $plugin ):
            $returnPlugin = $plugin;
        endforeach;

        return $returnPlugin;
    }

    /**
     * This function creates a register inside the database for a specific plugin.
     *
     * @since 0.5.0
     * 
     * @param string $name     The name of the plugin to be added.
     * @subpackage   GlassDB
     * @subpackage   Plugins
     */
    public function addPlugins( $name )
    {
        $sql = "INSERT INTO `plugins` (`name`) VALUE ('{$name}')";
        $stmt = $this->getConnection()->prepare($sql);

        $stmt->execute();
    }

    /**
     * This function adds a GlassPlugin obj plugin
     * to the @global $glassDB database abstraction object
     *
     * @since 0.6.0
     * 
     * @param      GlassPlugin $plugin
     * @subpackage GlassDB
     * @subpackage Plugins
     */
    public function addPlugin( GlassPlugin $plugin )
    {
        $this->plugins[ $plugin->getPluginName() ] = $plugin;
    }

    /**
     * This function returns an existing
     * GlassPlugin Object from the @global $glassDB.
     *
     * @since 0.6.0
     * 
     * @param      string $pluginName
     * @return     GlassPlugin          Returns a plugin.
     * @subpackage GlassDB
     * @subpackage Plugins
     */
    public function getPlugin( $pluginName )
    {
        //This is used for verifying and deleting existing data in DB about exluded plugins.
        if( ! isset( $this->plugins[ $pluginName ] ) && isset( $this->selectPluginNames()[ $pluginName ] ) ):
            $this->deletePlugin( $this->selectPluginNames()[ $pluginName ]['id'] );
        else:
            return $this->plugins[ $pluginName ];
        endif;
    }

    /**
     * This function activates a plugin in the program:
     *
     * @since 0.5.0
     *
     * @param integer $pluginId
     * @subpackage    GlassDB
     * @subpackage    Plugins
     */
    public function activatePlugin( $pluginId )
    {
        $stmt = $this->getConnection()->prepare("UPDATE `plugins` SET `activated` = '1' WHERE `plugins`.`id` = '".$pluginId."'");
        $stmt->execute();

        header('Location: plugins.php');
    }

    /**
     * This function deactivates an activated plugin (obviously) in the program:
     *
     * @since 0.5.0
     * 
     * @param integer $pluginId
     * @subpackage    GlassDB
     * @subpackage    Plugins
     */
    public function deactivatePlugin( $pluginId )
    {
        $stmt = $this->getConnection()->prepare("UPDATE `plugins` SET `activated` = '0' WHERE `plugins`.`id` = '".$pluginId."'");
        $stmt->execute();

        header('Location: plugins.php');
    }

    /**
     * This function manage several tasks for safely deleting a plugin.
     * 
     * 1 - It reads all the plugin paths (folders and subfolders) and its files.
     * 2 - Then, if those aren't empty it delete all files, then folders.
     * 3 - At last, it deletes the plugin data from the Glass DB.
     *
     * @since 0.5.0
     * 
     * @param integer $pluginId
     * @subpackage    GlassDB
     * @subpackage    Plugins
     */
    public function deletePlugin( $pluginId )
    {
        $pluginName = $this->selectThePlugin( $pluginId )[ 'name' ];

        $thePaths = null;
        $pathPrefix = PLUGINS_DIR . "{$pluginName}/";

        $stuffOnThePluginFolder = readFolders( $pathPrefix );

        if( empty($pluginName) ): return; endif;

        $thisPluginFiles   = $stuffOnThePluginFolder[ 'files' ];
        $thisPLuginFolders = $stuffOnThePluginFolder[ 'folders' ];

        if( ! empty( $thisPluginFiles ) ):
            deleteFiles( $thisPluginFiles );
        endif;
        if( ! empty( $thisPLuginFolders ) ):
            deleteFolders( $thisPLuginFolders );
        endif;

        $sql  = "DELETE FROM `plugins` WHERE `plugins`.`id` = '{$pluginId}'";
        $stmt = $this->getConnection()->prepare( $sql );
        $stmt->execute();

        header('Location: plugins.php');
        
    }

    /**
     * This function updates a specific plugin info inside the database
     *
     * @param integer $pluginId   The plugin id inside the database.
     * @param string  $property   The property to be updated.
     * @param mixed   $value      The value to be inserted in the property.
     * 
     * @subpackage GlassDB
     * @subpackage plugin
     */
    public function updatePluginInfo( $pluginId, $property, $value )
    {
        $stmt = $this->getConnection()->prepare("UPDATE `plugins` SET `{$property}` = '{$value}' WHERE `plugins`.`id` = '".$pluginId."'");
        $stmt->execute();
    }

    /**
     * this function creates a table inside the database
     *
     * @since 0.5.0
     * 
     * @param string $name  the table name.
     * @param array  $args  the parameters for the columns in the table.
     */
    private function createTable( $name, $args )
    {
        $tableName = $name;
        $tableIndexes = $args;

        $commandDefinitions = null;

        foreach( $tableIndexes as $attribute => $type ):
            $commandDefinitions .= "{$attribute} {$type} ";
        endforeach;

        $this->connection->exec( "CREATE TABLE {$tableName}({$commandDefinitions})" );
    }

    /**
     * This function creates a connection based on this object info
     * 
     * @since 0.5.0
     *
     * @return PDO connection
     */
    private function getConnection()
    {
        $host     = $this->db_host;
        $db_name  = $this->db_name;
        $user     = $this->db_user;
        $password = $this->db_password;

        $pdoArgs = 'mysql:host='.$host.';dbname='.$db_name;

        if( ! isset( $this->connection ) ):
            $this->connection = new PDO( $pdoArgs, $user, $password);
        endif;
        return $this->connection;
    }
}