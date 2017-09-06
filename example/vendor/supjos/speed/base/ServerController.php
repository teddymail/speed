<?php
/**
 * The ServerController.php class in Speed framework
 * License: MIT
 *
 * Speed framework use the ServerController to handle the RPC request, You can override the [[ init ]] method to
 * satisfied your need.
 *
 * Each request post to the ServerController will be passed to the method which controller derived from the
 * ServerController, For example:
 *
 * use supjos\base\ServerController;
 *
 * class RpcController extends ServerController
 * {
 *
 *      // This method will handle the request with the URL like [[ /rpc/good-list ]] or [[ /rpc/xxx/good-list ]] which
 *      // satisfied the PHP-PCRE regular expression
 *      public function goodList()
 *      {
 *          // xxxx
 *      }
 *
 *      // So this method handle the URL like [[ /rpc/good-price]] or  [[ /rpc/xxx/good-price ]]
 *      public function goodPrice()
 *      {
 *          // xxxx
 *      }
 *
 * }
 *
 * Created By: Josin 2017-09-04 21:01:29
 *
 * @since 1.0.6
 */

namespace supjos\base;

use App;
use supjos\exception\MethodNotExistsException;
use supjos\net\Request;
use supjos\tool\Inflector;
use function method_exists;

class ServerController extends RequestController
{
    
    /**
     * Do the request with your own controller in different class which derive from the [[ RequestController ]] or
     * [[ RequestInterface ]] interface, each class must implement the method named [[ init ]]
     *
     * @return mixed
     * @throws \supjos\exception\MethodNotExistsException
     */
    function init()
    {
        // You can override this method in you child class which derived from the [[ ServerController ]]
        $request = App::createObject( Request::getClass() );
        $method = $request->paramArg;
        
        // Run the method from the PATH-INFO
        $method = Inflector::id2Camel( $method );
        if ( method_exists( $this, $method ) ) {
            $this->$method();
        } else {
            throw new MethodNotExistsException( "Method {$method} Not Exists In " . static::getClass() .
                                                " Controller." );
        }
    }
    
}