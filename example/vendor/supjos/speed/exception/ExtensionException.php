<?php
/**
 * The ExtensionException.php class in Speed framework
 * License: MIT
 * Created By: Josin 2017-08-21 05:20:29
 *
 * @since 0.0.2
 */

namespace supjos\exception;

class ExtensionException extends BaseException
{
    
    public function __construct( $message, $code = 404, \Exception $previous = NULL )
    {
        parent::__construct( $message, $code, $previous );
    }
    
}