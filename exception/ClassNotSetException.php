<?php
/**
 * ------------------------------------
 *
 * Created by PhpStorm.
 *
 * User: Josin
 * Date: 2017/8/19
 * Time: 10:52
 * ------------------------------------
 *  www.supjos.cn All Rights Reserved.
 * ------------------------------------
 *
 */

namespace supjos\exception;

use Exception;

class ClassNotSetException extends BaseException
{
    
    public function __construct( $message, $code = 404, Exception $previous = NULL )
    {
        parent::__construct( $message, $code, $previous );
    }
    
    
}