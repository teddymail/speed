<?php

use supjos\exception\NotFoundException;
use supjos\exception\ParameterNotMatch;
use supjos\net\Request;
use supjos\net\Response;

/**
 * The core class in Speed framework
 * License: MIT
 * Created By: Josin 2017-8-18 19:02:29
 *
 * @since 0.0.2
 */
class App
{
    
    /**
     * The global namespace aliases for the Speed to load the class file correctly
     *
     * @var type
     */
    public static $aliases = [
        '@supjos' => __DIR__
    ];
    
    /**
     * The container contains the App::createObject's return result for the current temporary use
     *
     * @var array
     */
    private static $_containerSet = [];
    
    /**
     * The autoload function to load the needed class
     *
     * @param type $className
     *
     * @throws NotFoundException
     */
    public static function autoload( $className )
    {
        if ( !empty( $className ) ) {
            $parentPath = '';
            
            $slashPos = strpos( $className, '\\' );
            if ( $slashPos === FALSE ) {
                $nameAlias = $className;
            } else {
                $nameAlias = substr( $className, 0, $slashPos );
            }
            foreach ( static::$aliases as $name => $dir ) {
                if ( strcmp( $name, '@' . $nameAlias ) == 0 ) {
                    $parentPath = $dir;
                    break;
                }
            }
            $filePath = strtr( $parentPath . substr( $className, $slashPos ), '\\', '/' ) . '.php';
            if ( file_exists( $filePath ) ) {
                require_once( $filePath );
            } else {
                header( 'HTTP/1.1 404 NotFound' );
                throw new NotFoundException( "Class File {$filePath} Not Found" );
            }
        }
    }
    
    /**
     * @param $aliasPath The path which contains alias or not
     *
     * @return string return the absolute path
     * @throws NotFoundException
     */
    public static function getAliasPath( $aliasPath )
    {
        if ( !empty( $aliasPath ) ) {
            if ( substr( $aliasPath, 0, 1 ) == '@' ) {
                $slashPos = strpos( $aliasPath, '/' );
                if ( $slashPos === FALSE ) {
                    return $aliasPath;
                }
                $aliasName = static::getAlias( substr( $aliasPath, 0, $slashPos ) );
                
                return strtr( $aliasName . substr( $aliasPath, $slashPos ), '\\', '/' );
            } else {
                return $aliasPath;
            }
            
        } else {
            throw new NotFoundException( "{$aliasPath} null!" );
        }
    }
    
    /**
     * @param $aliasName The alias you want to get
     *
     * @return string|null
     */
    public static function getAlias( $aliasName )
    {
        if ( !empty( $aliasName ) ) {
            return static::getAliases()[ $aliasName ];
        } else {
            return NULL;
        }
    }
    
    /**
     * Return all the system aliases
     *
     * @return array
     */
    public static function getAliases()
    {
        return static::$aliases;
    }
    
    /**
     * Set a new global alias
     *
     * @param $aliasName
     * @param $aliasValue
     */
    public static function setAliases( $aliasName, $aliasValue )
    {
        if ( !empty( $aliasName ) && substr( $aliasName, 0, 1 ) == '@' ) {
            static::$aliases[ $aliasName ] = $aliasValue;
            
        }
    }
    
    /**
     * Return the WebPath for the view rendering
     *
     * @return string
     *
     * @throws NotFoundException
     * @throws ParameterNotMatch
     */
    public static function getWebPath()
    {
        return dirname( static::createObject( Request::getClass() )
                              ->getScriptName()
        );
    }
    
    /**
     * The universal object-create function
     *
     * @param string|array $class if the type of $class was array, the ````class```` key must be included
     *                                    the array $class contains ````class```` and the other key will passed to the
     *                                    constructor to initialise the constructor
     * @param array        $objectOptions The array contains all the property attaching to the creating-object
     *
     * @return null|object if create-process failed, return null, otherwise the object of the given class
     * @throws NotFoundException
     * @throws ParameterNotMatch
     * @since 0.0.1
     */
    public static function createObject( $class, $objectOptions = NULL )
    {
        if ( !empty( $class ) ) {
            
            if ( is_string( $class ) ) {
                $className = $class;
            } else if ( is_array( $class ) && isset( $class[ 'class' ] ) ) {
                $className = $class[ 'class' ];
                unset( $class[ 'class' ] );
            }
            
            // --- If the current container contains the needing object with the given className, return the Object at time
            if ( !empty( static::$_containerSet[ $className ] ) ) {
                return static::$_containerSet[ $className ];
            }
            
            $reflectionClass = new \ReflectionClass( $className );
            
            $reflectionContructor = $reflectionClass->getConstructor();
            
            if ( !is_null( $reflectionContructor ) && !$reflectionContructor->isPublic() ) {
                throw new NotFoundException( "Class {$className}'s constructor is not public." );
            }
            
            if ( !empty( $class ) && is_array( $class ) ) {
                $parameterCount = count( $reflectionContructor->getParameters() );
                if ( $parameterCount > count( $class ) ) {
                    throw new ParameterNotMatch( "Class {$className}'s constructor parameter didn't satisfied." );
                }
                $resultObject = $reflectionClass->newInstanceArgs( $class );
            } else {
                $resultObject = $reflectionClass->newInstanceArgs();
            }
            
            if ( !empty( $objectOptions ) ) {
                foreach ( $objectOptions as $propertyName => $propertyValue ) {
                    if ( !empty( $propertyName ) && property_exists( $resultObject, $propertyName ) ) {
                        $reflectionProperty = new \ReflectionProperty( $className, $propertyName );
                        $reflectionProperty->setAccessible( TRUE );
                        $reflectionProperty->setValue( $resultObject, $propertyValue );
                    }
                }
            }
            
            // --- After finishing the object-create process, save the object into the container set for the next use
            static::$_containerSet[ $className ] = $resultObject;
            
            return $resultObject;
        } else {
            return NULL;
        }
    }
    
    /**
     * Receive the HTTP GET request from the server and then call the $callBackFunc to do the job
     *
     * @param string  $getUrl The HTTP URI
     * @param Closure $callBackFunc The callback function to do the job
     */
    public function get( $getUrl, $callBackFunc )
    {
        $this->dealRequest( 'get', $getUrl, $callBackFunc );
    }
    
    /**
     * The universal Request method
     *
     * @param $method       The request method, Such as post, get, delete, options etc
     * @param $requestUrl   The request url which you want to deal with
     * @param $callBackFunc The request callback function, to deal with the logic
     */
    private function dealRequest( $method, $requestUrl, $callBackFunc )
    {
        $request = static::createObject( Request::getClass() );
        $response = static::createObject( Response::getClass() );
        if ( $request->{'is' . ucfirst( strtolower( $method ) )}() ) {
            preg_match( '/' . addcslashes( $requestUrl, '/' ) . '/', $request->getPathInfo(), $matches );
            if ( !empty( $matches ) || ( $requestUrl == '/' && empty( $request->getPathInfo() ) ) ) {
                if ( is_callable( $callBackFunc ) && $callBackFunc instanceof \Closure ) {
                    call_user_func_array( $callBackFunc, [
                                                           $request,
                                                           $response
                                                       ]
                    );
                }
            } else {
                $response->setStatusCode( 404 );
            }
        }
    }
    
    /**
     * Receive the HTTP POST request from the server and then call the $callBackFunc to do the job
     *
     * @param string  $postUrl The HTTP URI
     * @param Closure $callBackFunc The callback function to do the job
     */
    public function post( $postUrl, $callBackFunc )
    {
        $this->dealRequest( 'post', $postUrl, $callBackFunc );
    }
    
    /**
     * Receive the HTTP PUT request from the server and then call the $callBackFunc to do the job
     *
     * @param string  $putUrl The HTTP URI
     * @param Closure $callBackFunc The callback function to do the job
     */
    public function put( $putUrl, $callBackFunc )
    {
        $this->dealRequest( 'put', $putUrl, $callBackFunc );
    }
    
    /**
     * Receive the HTTP DELETE request from the server and then call the $callBackFunc to do the job
     *
     * @param string  $deleteUrl The HTTP URI
     * @param Closure $callBackFunc The callback function to do the job
     */
    public function delete( $deleteUrl, $callBackFunc )
    {
        $this->dealRequest( 'delete', $deleteUrl, $callBackFunc );
    }
    
    /**
     * Receive the HTTP OPTIONS request from the server and then call the $callBackFunc to do the job
     *
     * @param string  $optionsUrl The HTTP URI
     * @param Closure $callBackFunc The callback function to do the job
     */
    public function options( $optionsUrl, $callBackFunc )
    {
        $this->dealRequest( 'options', $optionsUrl, $callBackFunc );
    }
    
    /**
     * Receive the HTTP PATCH request from the server and then call the $callBackFunc to do the job
     *
     * @param string  $patchUrl The HTTP URI
     * @param Closure $callBackFunc The callback function to do the job
     */
    public function patch( $patchUrl, $callBackFunc )
    {
        $this->dealRequest( 'patch', $patchUrl, $callBackFunc );
    }
    
}

App::setAliases( '@webRoot', dirname( dirname( dirname( __DIR__ ) ) ) );

spl_autoload_register( [
                           'App',
                           'autoload'
                       ]
);
