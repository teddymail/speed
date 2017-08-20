<?php

/**
 * This is the supjos's Speed framework components named: Inflector
 * License : MIT
 * Copyright (c) 2017-2020 supjos.cn All Rights Reserved.
 *
 * @author Josin <774542602@qq.com | www.supjos.cn>
 */

namespace supjos\tool;

class Inflector
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
    
}
