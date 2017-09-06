<?php
/**
 * The RequestInterface.php class in Speed framework
 *
 * Each class in Speed which want to handle the request for the $app->[[method]](), must derive from this interface or
 * the subclass derive from [[ RequestController ]] or [[ ServerController ]]
 *
 * License: MIT
 * Created By: Josin 2017-09-03 19:22:29
 *
 * @since 0.0.2
 */

namespace supjos\base;


interface RequestInterface
{
    
    function init();
}