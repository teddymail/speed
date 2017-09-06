<?php
/**
 * ------------------------------------
 *
 * Created by PhpStorm.
 *
 * User: Josin
 * Date: 2017/8/19
 * Time: 10:48
 * ------------------------------------
 *  www.supjos.cn All Rights Reserved.
 * ------------------------------------
 *
 */

namespace supjos\exception;

use Exception;

class BaseException extends Exception
{
    
    public function __construct( $message, $code = 0, Exception $previous = NULL )
    {
        parent::__construct( $message, $code, $previous );
    }
    
    public function __toString()
    {
        return parent::__toString();
    }
    
}