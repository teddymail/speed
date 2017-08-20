<?php
/**
 * The Config.php class in Speed framework
 * License: MIT
 * Created By: Josin 2017-8-18 19:02:29
 *
 * @since 0.0.2
 */

namespace supjos\config;

use Object;
use supjos\exception\NotFoundException;

class Config extends Object
{
    /**
     * @var string $configFullName The config file's full path
     */
    private $configFullName;
    
    public function __construct( $configFile = '' )
    {
        if ( !empty( $configFile ) ) {
            $config = $configFile;
        } else {
            $config = 'config.php';
        }
        $this->configFullName = dirname( __DIR__ ) . DIRECTORY_SEPARATOR . $config;
        if ( !file_exists( $this->configFullName ) ) {
            throw new NotFoundException( "config file {$config} not found." );
        }
        
    }
    
    /**
     * @param string $configName The config values which you want to get from the config.php file
     *
     * @return null|array|mixed The config value you want to get, if exists.
     */
    public function getConfig( $configName )
    {
        $allConfigs = $this->getConfigs();
        if ( !empty( $allConfigs ) && isset( $allConfigs[ $configName ] ) ) {
            return $allConfigs[ $configName ];
        } else {
            return NULL;
        }
    }
    
    /**
     * All the config values from the config file
     *
     * @return mixed|array|null
     */
    public function getConfigs()
    {
        return require( $this->configFullName );
    }
}