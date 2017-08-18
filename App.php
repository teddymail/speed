<?php

namespace supjos;

class App
{

    /**
     * The autoload function to load the needed class
     * @param type $className
     */
    public static function autoload($className)
    {
        echo $className;
    }

}
