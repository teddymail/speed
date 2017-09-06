<?php

/**
 * This is the supjos's Speed framework components named: Inflector
 * License : MIT
 * Copyright (c) 2017-2020 supjos.cn All Rights Reserved.
 *
 * @author Josin <774542602@qq.com | www.supjos.cn>
 * @modify 2017-09-04 22:48:21
 */

namespace supjos\tool;

use Object;
use function array_map;
use function strtolower;
use function ucfirst;

class Inflector extends Object
{
    
    /**
     * Return the camel2Id string. eg.
     * userName ===> User Name
     *
     * @param $string
     *
     * @return string
     */
    public static function camel2Id( $string )
    {
        $result = [];
        \preg_match_all( '/[A-Z][^A-Z]+/', ucfirst( $string ), $result );
        
        return implode( ' ', $result[ 0 ] );
    }
    
    /**
     * @param        $string
     * @param string $concatChar
     *
     * @return string Return the result
     */
    public static function camel2Ids( $string, $concatChar = '-' )
    {
        $result = [];
        preg_match_all( '/[A-Z][^A-Z]+/', ucfirst( $string ), $result );
        
        return strtolower( implode( $concatChar, $result[ 0 ] ) );
    }
    
    /**
     * @param        $string
     * @param string $concatChar
     *
     * @return string Return the camel2Id string to id2Camel string
     */
    public static function id2Camel( $string, $concatChar = '-' )
    {
        return lcfirst( implode( '', array_map( function ( $eachValue ) {
            return ucfirst( $eachValue );
        }, explode( $concatChar, $string ) ) ) );
    }
}
