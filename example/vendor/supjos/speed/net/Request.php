<?php

/**
 * This is Request tool class of the Speed framework
 * License : MIT
 * Copyright (c) 2017-2020 supjos.cn All Rights Reserved.
 *
 * @author Josin <774542602@qq.com | www.supjos.cn>
 *
 * @since 1.0.6 fixed the getPathInfo() bug : get the REDIRECT_PATH_INFO variable
 */

namespace supjos\net;

use function apache_request_headers;
use function function_exists;
use function is_string;
use function ltrim;
use function str_replace;
use function strtolower;

/**
 * The Request class means the HTTP request object
 * License: MIT
 * Created By Josin 2017-8-18 20:11:15
 *
 * @since 0.0.1
 */
class Request extends \Object
{
    
    /**
     * @var null|mixed When in REST|RPC mode, this property will be set to the last PATH_INFO parameters
     */
    public $paramArg = NULL;
    
    /**
     * Get the HTTP request header
     *
     * @param $headerName
     *
     * @return string|null The header value of the given header name
     */
    public function getHeader( $headerName )
    {
        $headers = $this->getHeaders();
        if ( isset( $headers[ $headerName ] ) ) {
            return $headers[ $headerName ];
        } else {
            return NULL;
        }
    }
    
    /**
     * Get all the reqeust headers
     *
     * @return array
     */
    public function getHeaders()
    {
        if ( !function_exists( 'apache_request_headers' ) ) {
            return $this->getRequestHeaders();
        } else {
            return apache_request_headers();
        }
    }
    
    /**
     * To instead the apache_request_headers using the getRequestHeaders function
     *
     * @return mixed
     */
    private function getRequestHeaders()
    {
        foreach ( $_SERVER as $headerName => $headerValue ) {
            if ( substr( $headerName, 0, 5 ) == 'HTTP_' ) {
                $headers[ str_replace( ' ', '-',
                                       ucwords( strtolower( str_replace( '-', ' ', substr( $headerName, 5 ) ) ) ) ) ] =
                    $headerValue;
            }
        }
        
        return $headers;
    }
    
    /**
     * Check whether the current HTTP request method is GET
     *
     * @return bool true GET request, otherwise fales
     */
    public function isGet()
    {
        return $this->getRequestMethod() === 'GET';
    }
    
    /**
     * To get the HTTP request method, default method is ```GET```
     *
     * @return string The HTTP request method
     */
    public function getRequestMethod()
    {
        if ( isset( $_SERVER[ 'HTTP_X_HTTP_METHOD_OVERRIDE' ] ) ) {
            return strtoupper( $_SERVER[ 'HTTP_X_HTTP_METHOD_OVERRIDE' ] );
        }
        
        if ( isset( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
            return strtoupper( $_SERVER[ 'REQUEST_METHOD' ] );
        }
        
        return 'GET';
    }
    
    /**
     * Returns whether this is a PJAX request
     *
     * @return bool whether this is a PJAX request
     */
    public function isPjax()
    {
        return $this->isAjax() && !empty( $_SERVER[ 'HTTP_X_PJAX' ] );
    }
    
    /**
     * To judge the HTTP request whether the request is a Ajax request
     *
     * @return bool true Ajax request, otherwise false
     */
    public function isAjax()
    {
        return isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] === 'XMLHttpRequest';
    }
    
    /**
     * Check whether the current HTTP request method is POST
     *
     * @return bool true POST request, otherwise false
     */
    public function isPost()
    {
        return $this->getRequestMethod() === 'POST';
    }
    
    /**
     * Check whether the current HTTP request method is PUT
     *
     * @return bool true PUT request, otherwise false
     */
    public function isPut()
    {
        return $this->getRequestMethod() === 'PUT';
    }
    
    /**
     * Check whether the current HTTP request method is HEAD
     *
     * @return bool true HEAD request, otherwise false
     */
    public function isHead()
    {
        return $this->getRequestMethod() === 'HEAD';
    }
    
    /**
     * Check whether the current HTTP request method is OPTION
     *
     * @return bool true OPTION request, otherwise false
     */
    public function isOptions()
    {
        return $this->getRequestMethod() === 'OPTIONS';
    }
    
    /**
     * Check whether the current HTTP request method is DELETE
     *
     * @return bool true DELETE request, otherwise false
     */
    public function isDelete()
    {
        return $this->getRequestMethod() === 'DELETE';
    }
    
    /**
     * Check whether the current HTTP request method is PATCH
     *
     * @return bool true PATCH request, otherwise false
     */
    public function isPatch()
    {
        return $this->getRequestMethod() === 'PATCH';
    }
    
    /**
     * Get the current http request
     *
     * @return null|string The request uri from the current http request
     */
    public function getRequestUrl()
    {
        if ( isset( $_SERVER[ 'REQUEST_URI' ] ) ) {
            return $_SERVER[ 'REQUEST_URI' ];
        } else {
            return NULL;
        }
    }
    
    /**
     * Get the current http request
     *
     * @return null|string The request QueryString from the current http request
     */
    public function getQueryString()
    {
        if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
            return $_SERVER[ 'QUERY_STRING' ];
        } else {
            return NULL;
        }
    }
    
    /**
     * Get the current http request
     *
     * @return null|string The request ScriptName from the current http request
     */
    public function getScriptName()
    {
        if ( isset( $_SERVER[ 'SCRIPT_NAME' ] ) ) {
            return $_SERVER[ 'SCRIPT_NAME' ];
        } else {
            return NULL;
        }
    }
    
    /**
     * Get the current http request
     *
     * @return null|string The request PHP_SELF from the current http request
     */
    public function getPhpSelf()
    {
        if ( isset( $_SERVER[ 'PHP_SELF' ] ) ) {
            return $_SERVER[ 'PHP_SELF' ];
        } else {
            return NULL;
        }
    }
    
    /**
     * Get the current http request
     *
     * @return null|string The request REQUEST_TIME from the current http request
     */
    public function getRequestTime()
    {
        if ( isset( $_SERVER[ 'REQUEST_TIME' ] ) ) {
            return $_SERVER[ 'REQUEST_TIME' ];
        } else {
            return NULL;
        }
    }
    
    /**
     * Get the current http request
     *
     * @return null|string The request REMOTE_PORT from the current http request
     */
    public function getRemotePort()
    {
        if ( isset( $_SERVER[ 'REMOTE_PORT' ] ) ) {
            return $_SERVER[ 'REMOTE_PORT' ];
        } else {
            return NULL;
        }
    }
    
    /**
     * Get the current http request
     *
     * @return null|string The request SERVER_PROTOCOL from the current http request
     */
    public function getServerProtocol()
    {
        if ( isset( $_SERVER[ 'SERVER_PROTOCOL' ] ) ) {
            return $_SERVER[ 'SERVER_PROTOCOL' ];
        } else {
            return NULL;
        }
    }
    
    /**
     * Get the current http request
     *
     * @return null|string The request REQUEST_SCHEME from the current http request
     */
    public function getRequestScheme()
    {
        if ( isset( $_SERVER[ 'REQUEST_SCHEME' ] ) ) {
            return $_SERVER[ 'REQUEST_SCHEME' ];
        } else {
            return NULL;
        }
    }
    
    /**
     * Get the current http request
     *
     * @return null|string The request HTTP_HOST from the current http request
     */
    public function getHost()
    {
        if ( isset( $_SERVER[ 'HTTP_HOST' ] ) ) {
            return $_SERVER[ 'HTTP_HOST' ];
        } else {
            return NULL;
        }
    }
    
    /**
     * Get the current http request
     *
     * @return null|string The request SERVER_ADDR from the current http request
     */
    public function getServerAddr()
    {
        if ( isset( $_SERVER[ 'SERVER_ADDR' ] ) ) {
            return $_SERVER[ 'SERVER_ADDR' ];
        } else {
            return NULL;
        }
    }
    
    /**
     * Get the current http request
     *
     * @return null|string The request SERVER_PORT from the current http request
     */
    public function getServerPort()
    {
        if ( isset( $_SERVER[ 'SERVER_PORT' ] ) ) {
            return $_SERVER[ 'SERVER_PORT' ];
        } else {
            return NULL;
        }
    }
    
    /**
     * Get the current http request
     *
     * @return null|string The request PATH_INFO from the current http request
     */
    public function getPathInfo()
    {
        if ( isset( $_SERVER[ 'PATH_INFO' ] ) ) {
            return $_SERVER[ 'PATH_INFO' ];
        } elseif ( isset( $_SERVER[ 'REDIRECT_PATH_INFO' ] ) ) {
            return '/' . ltrim( $_SERVER[ 'REDIRECT_PATH_INFO' ], '/' );
        } else {
            return NULL;
        }
    }
    
    /**
     * @param string $getKey The GET key to obtain the get value
     *
     * @return null|array|string|mixed The value you want to obtain, or null means nothing
     */
    public function get( $getKey = '' )
    {
        if ( empty( $getKey ) ) {
            return $_GET;
        } else if ( is_string( $getKey ) ) {
            if ( isset( $_GET[ $getKey ] ) ) {
                return $_GET[ $getKey ];
            }
        }
        
        return NULL;
    }
    
    /**
     * @param string $postKey The POST key to obtain the get value
     *
     * @return null|array|string|mixed The value you want to obtain, or null means nothing
     */
    public function post( $postKey = '' )
    {
        if ( empty( $postKey ) ) {
            return $_POST;
        } else if ( is_string( $postKey ) ) {
            if ( isset( $_POST[ $postKey ] ) ) {
                return $_POST[ $postKey ];
            }
        }
        
        return NULL;
    }
    
    /**
     * @param string $serverKey The SERVER key to obtain the get value
     *
     * @return null|array|string|mixed The value you want to obtain, or null means nothing
     */
    public function server( $serverKey = '' )
    {
        if ( empty( $serverKey ) ) {
            return $_SERVER;
        } else if ( is_string( $serverKey ) ) {
            if ( isset( $_SERVER[ $serverKey ] ) ) {
                return $_SERVER[ $serverKey ];
            }
        }
        
        return NULL;
    }
    
    
}
