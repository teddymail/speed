<?php
/**
 * ------------------------------------
 *
 * Created by PhpStorm.
 *
 * User: Josin
 * Date: 2017/8/19
 * Time: 10:49
 * ------------------------------------
 *  www.supjos.cn All Rights Reserved.
 * ------------------------------------
 *
 */

namespace supjos\exception;

use Exception;

class ParameterNotMatch extends BaseException
{
    
    public function __construct( $message, $code = 404, Exception $previous = NULL )
    {
        parent::__construct( $message, $code, $previous );
    }
    
    
}