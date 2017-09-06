<?php
/**
 * The ClassImplementException.php class in Speed framework
 * License: MIT
 * Created By: Josin 2017-09-03 19:32:29
 *
 * @since 0.0.2
 */

namespace supjos\exception;

class ClassImplementException extends BaseException
{
    
    public function __construct( $message, $code = 404, \Exception $previous = NULL )
    {
        parent::__construct( $message, $code, $previous );
    }
    
}