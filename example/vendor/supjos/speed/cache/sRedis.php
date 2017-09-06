<?php
/**
 * The Redis.php class in Speed framework
 * License: MIT
 * Created By: Josin 2017-08-21 05:17:29
 *
 * @since 0.0.2
 */

namespace supjos\cache;

use App;
use Object;
use supjos\config\Config;
use supjos\exception\ExtensionException;
use supjos\exception\NotFoundException;
use function extension_loaded;

class sRedis extends Object
{
    
    /**
     * @var null|\Redis The instance of the Redis
     */
    private static $redisInstance = NULL;
    
    /**
     * sRedis constructor.
     */
    private function __construct()
    {
        
    }
    
    /**
     * @return null|\Redis The redis instance
     * @throws \Exception
     * @throws \supjos\exception\ExtensionException
     * @throws \supjos\exception\NotFoundException
     */
    public static function getRedis()
    {
        if ( !extension_loaded( 'redis' ) ) {
            throw new ExtensionException( "Extension redis not installed." );
        }
        if ( static::$redisInstance === NULL ) {
            
            static::$redisInstance = new \Redis();
            $config = App::createObject( Config::getClass() );
            $configDb = $config->getConfig( 'redis' );
            
            if ( empty( $configDb ) && empty( $configDb[ 'host' ] ) ) {
                
                throw new NotFoundException( "Redis config not found in config file." );
                
            }
            
            static::$redisInstance->connect( $configDb[ 'host' ], $configDb[ 'port' ] );
        }
        
        return static::$redisInstance;
    }
}