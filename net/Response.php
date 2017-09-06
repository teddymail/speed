<?php

/**
 * This is the PHP framework of the Speed
 * The Response object means the HTTP response object
 * License : MIT
 * Copyright (c) 2017-2020 supjos.cn All Rights Reserved.
 *
 * @author Josin <774542602@qq.com | www.supjos.cn>
 */

namespace supjos\net;

use function apache_response_headers;

class Response extends \Object
{
    
    /**
     * Set the HTTP response header to the current request
     *
     * @param array $headerArray The HTTP headers you want to set
     */
    public function setHeaders( $headerArray )
    {
        if ( is_array( $headerArray ) && !empty( $headerArray ) ) {
            foreach ( $headerArray as $headerName => $headerValue ) {
                $this->setHeader( $headerName, $headerValue );
            }
        }
    }
    
    /**
     * @param string $headerName  The HTTP header name you want to set
     * @param string $headerValue The corresponding HTTP header value will be set
     */
    public function setHeader( $headerName, $headerValue )
    {
        if ( !empty( $headerName ) && is_string( $headerName ) ) {
            header( "{$headerName}:{$headerValue}" );
        }
    }
    
    /**
     * @param array|mixed $endData The data you want to sent to the browser or client user
     */
    public function end( $endData )
    {
        $request = new Request();
        if ( $request->getHeader( 'Accept' ) == 'application/json' ) {
            $this->setStatusCode( 200 );
            $this->setHeader( 'Content-Type', 'application/json;charset=UTF-8' );
            echo json_encode( $endData );
        } elseif ( $request->getHeader( 'Accept' ) == 'application/xml' ) {
            $this->setStatusCode( 200 );
            $this->setHeader( 'Content-Type', 'application/xml;charset=UTF-8' );
            echo $this->formatXml( $endData, '', TRUE );
        } else {
            $this->setStatusCode( 200 );
            echo $endData;
        }
        exit( 0 );
    }
    
    /**
     * Set the HTTP response statusCode for the current request
     *
     * @param int $statusCode The statusCode you want to set
     *
     * @return bool|int if the statusCode successfully set, it will return the previous statusCode,otherwise return
     *                  the current statusCode
     */
    public function setStatusCode( $statusCode )
    {
        if ( is_int( $statusCode ) ) {
            return http_response_code( $statusCode );
        } else {
            return FALSE;
        }
    }
    
    /**
     * Change the array data into xml format
     *
     * @param array       $data      The data
     * @param string      $head      The head tag
     * @param boolean     $simplexml Generate the simple xml, recommend simple-xml
     *
     * @param bool|string $end
     *
     * @return string
     */
    private function formatXml( $data, $head = '', $simplexml = FALSE, $end = TRUE )
    {
        $str = $head;
        if ( $head !== NULL ) {
            if ( $simplexml ) {
                $str = '<xml><response>';
            } else {
                $str = '<?xml version="1.0" encoding="UTF-8"?><response>';
            }
        }
        if ( is_string( $data ) ) {
            $data = [$data];
        }
        foreach ( $data as $key => $val ) {
            if ( is_array( $val ) ) {
                $child = $this->formatXml( $val, NULL, $simplexml, FALSE );
                // $str .= "<{$key}>" . $child . "</{$key}>";
                $str .= "<item>" . $child . "</item>";
            } else {
                if ( is_numeric( $val ) ) {
                    if ( is_numeric( $key ) ) {
                        $str .= "<item>{$val}</item>";
                    } else {
                        $str .= "<{$key}>{$val}</{$key}>";
                    }
                } else {
                    if ( is_numeric( $key ) ) {
                        $str .= "<item><![CDATA[{$val}]]></item>";
                    } else {
                        $str .= "<{$key}><![CDATA[{$val}]]></{$key}>";
                    }
                }
            }
        }
        
        if ( $end ) {
            
            if ( $head !== NULL ) {
                
                $str .= '</response>';
            }
            
            if ( $simplexml ) {
                $str .= '</xml>';
            }
        }
        
        return $str;
    }
    
    /**
     * Write JSON response data to server or client
     *
     * @param array|mixed $endData The data which you want to change into JSON format
     */
    public function asJson( $endData )
    {
        if ( !empty( $endData ) ) {
            $this->setStatusCode( 200 );
            $this->setHeader( 'Content-Type', 'application/json;charset=UTF-8' );
            exit( json_encode( $endData ) );
        }
    }
    
    /**
     * Write XML data to the client
     *
     * @param mixed|array|string $endData   The data which you want to turn into xml format
     * @param bool               $simpleXml The format of the response xml, ```simpleXml``` or not
     */
    public function asXml( $endData, $simpleXml = TRUE )
    {
        if ( !empty( $endData ) ) {
            $this->setStatusCode( 200 );
            $this->setHeader( 'Content-Type', 'application/xml;charset=UTF-8' );
            exit( $this->formatXml( $endData, '', $simpleXml ) );
        }
    }
    
    /**
     * @param string $headerName The header value you want to get from the response
     *
     *
     * @return mixed|null
     */
    public function getHeader( $headerName )
    {
        $headers = $this->getHeaders();
        if ( isset( $headers[ $headerName ] ) ) {
            return $headers[ $headerName ];
        } elseif ( isset( $_SERVER[ 'HTTP_' . strtoupper( $headerName ) ] ) ) {
            return $_SERVER[ 'HTTP_' . strtoupper( $headerName ) ];
        } else {
            return NULL;
        }
    }
    
    /**
     * Get all the HTTP response headers
     *
     * @return array The result headers
     */
    public function getHeaders()
    {
        if ( !function_exists( 'apache_response_headers' ) ) {
            $resultResponseHeaders = [];
            $headersList = headers_list();
            foreach ( $headersList as $header ) {
                $header = explode( ":", $header );
                $resultResponseHeaders[ array_shift( $header ) ] = trim( implode( ":", $header ) );
            }
            
            return $resultResponseHeaders;
        } else {
            return apache_response_headers();
        }
    }
    
    /**
     * @param $renderTemplate
     * @param $variables
     *
     * @return string The result of the HTML Data to the browser
     */
    public function render( $renderTemplate, $variables = NULL )
    {
        echo $this->getObClean( $renderTemplate, $variables );
    }
    
    /**
     * It was the ```private``` method for the Speed to render the template file
     *
     * @param            $fileAlias
     * @param array|NULL $variables
     *
     * @return string The HTML data from the rendering process
     */
    private function getObClean( $fileAlias, array $variables = NULL )
    {
        ob_start();
        ob_implicit_flush( FALSE );
        extract( $variables );
        $filePath = \App::getAliasPath( $fileAlias );
        if ( file_exists( $filePath . '.php' ) ) {
            require( $filePath . '.php' );
        }
        
        return ob_get_clean();
    }
    
    /**
     * To render the response HTML result
     *
     * @param      $renderTemplate
     * @param null $variables
     *
     * @return string The rendering result of the response
     */
    public function getRenderResult( $renderTemplate, $variables = NULL )
    {
        return $this->getObClean( $renderTemplate, $variables );
    }
    
    
}