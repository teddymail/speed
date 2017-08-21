<?php
/**
 * The Cookie.php class in Speed framework
 * License: MIT
 * Created By: Josin 2017-08-20 21:59:29
 *
 * The cookie class to manage the cookie
 *
 * @since 0.0.2
 */

namespace supjos\cache;

use Object;
use function setcookie;

class Cookie extends Object
{
    
    /**
     * @param $cookieName  The cookie name you want to judge the cookie value
     * @param $cookieValue The corresponding value of the cookie key
     *
     * @return bool true operation success, otherwise false
     */
    public function setCookie( $cookieName, $cookieValue )
    {
        if ( is_string( $cookieName ) && !empty( $cookieName ) ) {
            setcookie( $cookieName, $cookieValue );
            
            return TRUE;
        }
        
        return FALSE;
    }
    
    /**
     * @param string $cookieName The cookie name you want to get the cookie value
     *
     * @return bool|string|array|mixed The false means the cookie not exists, or unavailable, otherwise return the
     *                                 cookie value
     */
    public function getCookie( $cookieName )
    {
        if ( is_string( $cookieName ) && !empty( $cookieName ) ) {
            if ( isset( $_COOKIE[ $cookieName ] ) ) {
                return $_COOKIE[ $cookieName ];
            }
            
            return FALSE;
        }
        
        return FALSE;
    }
    
    /**
     * @param array|string $cookieKeys ```array``` means to remove two or more session value, ```string``` means only
     *                                 one
     *
     * @return bool true successfully remove the session value, otherwise false
     */
    public function removeCookieKeys( $cookieKeys )
    {
        if ( is_string( $cookieKeys ) && !empty( $cookieKeys ) ) {
            if ( isset( $_COOKIE[ $cookieKeys ] ) ) {
                unset( $_COOKIE[ $cookieKeys ] );
            }
            
            return TRUE;
        } else if ( is_array( $cookieKeys ) && !empty( $cookieKeys ) ) {
            foreach ( $cookieKeys as $cookieKey ) {
                $this->removeCookieKeys( $cookieKey );
            }
            
            return TRUE;
        }
        
        return FALSE;
    }
}