<?php
/**
 * The ParseException.php class in Speed framework
 * License: MIT
 * Created By: Josin 2017-08-21 04:58:29
 *
 * @since 0.0.2
 */

namespace supjos\exception;

class ParseException extends BaseException
{
    
    public function __construct( $message, $code = 404, \Exception $previous = NULL )
    {
        parent::__construct( $message, $code, $previous );
    }
    
}