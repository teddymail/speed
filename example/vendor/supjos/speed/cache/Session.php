<?php
/**
 * The Session.php class in Speed framework
 * License: MIT
 * Created By: Josin 2017-08-20 21:17:29
 *
 * The ```Session``` management class which you can use to manage your session data
 *
 * @since 0.0.2
 */

namespace supjos\cache;

use Object;
use supjos\exception\SessionException;
use const TRUE;
use function is_string;
use function session_id;

class Session extends Object
{
    
    /**
     * Session constructor.
     * To start the session
     *
     * @param null  $sessionId      , You can pass the sessionId in the contructor
     *
     * @param array $sessionOptions The session Options to initialise the session class
     *
     * @throws \supjos\exception\SessionException
     */
    public function __construct( $sessionId = NULL, $sessionOptions = [] )
    {
        if ( !empty( $sessionId ) ) {
            session_id( $sessionId );
        }
        if ( !session_start( $sessionOptions ) ) {
            throw new SessionException( "Can't start session." );
        }
    }
    
    /**
     * @param $sessionKey   The session-key you can access the session data
     * @param $sessionValue The corresponding value for the session-key
     *
     * @return bool true set the session success, otherwise false
     */
    public function setSession( $sessionKey, $sessionValue )
    {
        if ( is_string( $sessionKey ) && !empty( $sessionKey ) ) {
            $_SESSION[ $sessionKey ] = $sessionValue;
            
            return TRUE;
        }
        
        return FALSE;
    }
    
    /**
     * @param $sessionkey The session key which you want to get the session data
     *
     * @return null|mixed The value from the session with the session key
     */
    public function getSession( $sessionkey )
    {
        if ( is_string( $sessionkey ) && !empty( $sessionkey ) ) {
            if ( isset( $_SESSION[ $sessionkey ] ) ) {
                return $_SESSION[ $sessionkey ];
            } else {
                return NULL;
            }
        }
        
        return NULL;
    }
    
    /**
     * @param array|string $sessionKeys ```array``` means to remove two or more session value, ```string``` means
     *                                  only one
     *
     * @return bool true successfully remove the session value, otherwise false
     */
    public function removeSessionKeys( $sessionKeys )
    {
        if ( is_string( $sessionKeys ) && !empty( $sessionKeys ) ) {
            if ( isset( $_SESSION[ $sessionKeys ] ) ) {
                unset( $_SESSION[ $sessionKeys ] );
            }
            
            return TRUE;
        } else if ( is_array( $sessionKeys ) && !empty( $sessionKeys ) ) {
            foreach ( $sessionKeys as $sessionKey ) {
                $this->removeSessionKeys( $sessionKey );
            }
            
            return TRUE;
        }
        
        return FALSE;
    }
    
    /**
     * @return string Return the current session_id
     */
    public function getSessionId()
    {
        return session_id();
    }
    
}