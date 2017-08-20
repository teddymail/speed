<?php

namespace supjos\di;

use App;
use supjos\exception\ClassNotSetException;
use supjos\exception\ParameterNotMatch;

/**
 * The reflex tool class for the PHPer to do the object-create-process, and the initialise process
 *
 * @copyright (c) 2017-2020, www.supjos.cn All Rights Reserved.
 * @version       1.2.2
 */
class Reflex
{
    
    /**
     * @var array $_container
     */
    private $_container = [];
    /**
     * @var array The collections of the temperary create object process, To accessory the object-create time
     */
    private $_objects = [];
    
    /**
     * Invoke the method of the given class-name, return the method result
     *
     * @param string|object $className The class-name or class object
     * @param string        $method The method which you want to invoke from the given class-name
     * @param array         $params The parameters which you passed to the method
     *
     * @return mixed The result of the method return
     * @throws ParameterNotMatch
     */
    public static function invoke( $className, $method, $params = [] )
    {
        $result = '';
        $classMethod = new \ReflectionMethod( $className, $method );
        if ( $classMethod->isStatic() ) {
            $result = $classMethod->invokeArgs( NULL, $params );
        }
        if ( $classMethod->isPublic() && !$classMethod->isStatic() ) {
            if ( is_object( $className ) && $className instanceof $className ) {
                $classObject = $className;
            } else {
                $classObject = ( new \ReflectionClass( $className ) )->newInstanceArgs();
            }
            // Checking the method's parameters suit well
            if ( count( $classMethod->getParameters() ) > count( $params ) ) {
                throw new ParameterNotMatch( "Parameter Not Match!" );
            }
            $result = $classMethod->invokeArgs( $classObject, $params );
        }
        
        return $result;
    }
    
    /**
     * @param string $className
     * @param array  $configs
     */
    public function set( $className, $configs = [] )
    {
        if ( !empty( $className ) && !isset( $this->_container[ $className ] ) ) {
            $this->_container[ $className ] = empty( $configs ) ? [] : $configs;
        }
    }
    
    /**
     * @param string $className , Which class object you want to get from the container
     *
     * @return Object
     * @throws ClassNotSetException
     */
    public function get( $className )
    {
        if ( isset( $this->_objects[ $className ] ) && is_object( $this->_objects[ $className ] ) ) {
            return $this->_objects[ $className ];
        } else {
            $params = empty( $this->_container[ $className ] ) ? [] : $this->_container[ $className ];
            $reflectionClass = new \ReflectionClass( $className );
            $classContructor = $reflectionClass->getConstructor();
            if ( is_null( $classContructor ) ) {
                return $this->_objects[ $className ] = App::createObject( $className, $params );
            } else {
                $constructorParameters = $classContructor->getParameters();
                $dependencyClassName = $constructorParameters[ 0 ]->getClass()
                                                                  ->getName();
                if ( isset( $this->_container[ $dependencyClassName ] ) ) {
                    $classData = [
                        'class' => $className,
                        $this->get( $dependencyClassName )
                    ];
                    
                    return $this->_objects[ $className ] = App::createObject( $classData, $params );
                } else {
                    throw new ClassNotSetException( "class [[{$dependencyClassName}]] must be set before get()!" );
                }
            }
        }
    }
    
}
