<?php
/**
 * The MethodNotExistsException.php class in Speed framework
 * License: MIT
 * Created By: Josin 2017-09-04 21:04:29
 *
 * @since 0.0.2
 */
namespace supjos\exception;

use Exception;

class MethodNotExistsException extends BaseException
{
    
    public function __construct( $message, $code = 404, \Exception $previous = NULL )
    {
        parent::__construct( $message, $code, $previous );
    }
    
}