<?php
/**
 * The RequestController.php class in Speed framework
 *
 * Each [[ RequestController ]] class was the request handler to replace the default closure to deal with the logic
 * request
 *
 * REMEMBER : Each class derive from the class [[ RequestController ]] must implements the method named  ```[[ init
 * ]]```
 * For example :
 *
 * use supjos\base\RequestController;
 * class RestController extends RequestController
 * {
 *
 *     public function init()
 *     {
 *          // To add your own code.
 *     }
 * }
 *
 *
 * License: MIT
 * Created By: Josin 2017-09-03 19:24:29
 *
 * @since 0.0.3
 */

namespace supjos\base;

use Object;

abstract class RequestController extends Object implements RequestInterface
{
    
    /**
     * Do the request with your own controller in different class which derive from the [[ RequestController ]] or
     * [[ RequestInterface ]] interface, each class must implement the method named [[ init ]]
     *
     * @return mixed
     */
    abstract function init();
}